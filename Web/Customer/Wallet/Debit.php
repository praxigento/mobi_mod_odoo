<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Customer\Wallet;

use Praxigento\Core\Api\App\Web\Response\Result as WResult;
use Praxigento\Odoo\Api\Web\Customer\Wallet\Debit\Request as WRequest;
use Praxigento\Odoo\Api\Web\Customer\Wallet\Debit\Response as WResponse;
use Praxigento\Odoo\Api\Web\Customer\Wallet\Debit\Response\Data as WData;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Helper\Code\Request as HCodeReq;
use Praxigento\Odoo\Repo\Data\Registry\Request as ERegRequest;
use Praxigento\Wallet\Config as CfgWallet;

/**
 * API adapter to internal services to transfer funds from customer wallet to system wallet.
 */
class Debit
    implements \Praxigento\Odoo\Api\Web\Customer\Wallet\DebitInterface {
    private $allowedCurs = [Cfg::CODE_CUR_EUR, Cfg::CODE_CUR_USD, Cfg::CODE_CUR_RUB];

    /** @var \Praxigento\Accounting\Repo\Dao\Account */
    private $daoAcc;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Odoo\Repo\Dao\Registry\Request */
    private $daoRegRequest;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoTypeAsset;
    /** @var \Praxigento\Accounting\Api\Helper\Balance */
    private $hlpAccBalance;
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Accounting\Api\Service\Operation\Create */
    private $servOper;
    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    private $daoGeneric;
    /**
     * @var array [FROM][TO]=> rate
     */
    private $cacheRates = [];

    public function __construct(
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric,
        \Praxigento\Accounting\Repo\Dao\Account $daoAcc,
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoTypeAsset,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Odoo\Repo\Dao\Registry\Request $daoRegRequest,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Api\Helper\Balance $hlpAccBalance,
        \Praxigento\Accounting\Api\Service\Operation\Create $servOper
    ) {
        $this->logger = $logger;
        $this->daoGeneric = $daoGeneric;
        $this->daoAcc = $daoAcc;
        $this->daoTypeAsset = $daoTypeAsset;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->daoRegRequest = $daoRegRequest;
        $this->manTrans = $manTrans;
        $this->hlpAccBalance = $hlpAccBalance;
        $this->servOper = $servOper;
    }

    /**
     * Load rates from DB or use cache.
     *
     * @param string $from
     * @param string $to
     * @return float
     * @throws \Exception
     */
    private function loadRate($from, $to) {
        if (isset($this->cacheRates[$from][$to])) {
            $result = $this->cacheRates[$from][$to];
        } else {
            $entity = Cfg::ENTITY_MAGE_DIR_CUR_RATE;
            $key = [
                Cfg::E_DIR_CUR_RATE_A_CURRENCY_FROM => $from,
                Cfg::E_DIR_CUR_RATE_A_CURRENCY_TO => $to
            ];
            $found = $this->daoGeneric->getEntityByPk($entity, $key);
            if ($found) {
                $result = $found[Cfg::E_DIR_CUR_RATE_A_RATE];
                $this->cacheRates[$from][$to] = $result;
            } else {
                throw new \Exception("Cannot load rate for currencies: $from => $to.");
            }
        }
        return $result;
    }

    /**
     * Convert $amount if $currency equals to EUR.
     *
     * @param $amount
     * @param $currency
     * @return float
     */
    private function convertAmountToBaseCurrency($amount, $currency) {
        if ($currency == Cfg::CODE_CUR_EUR) {
            // Yes, this is an ugly solution, I know. Wrong reference to top-level plugin.
            $result = $amount * \Praxigento\Santegra\Config::RATE_EUR_USD_AFTER_20220901;
        } elseif ($currency == Cfg::CODE_CUR_RUB) {
            $rate = $this->loadRate($currency, Cfg::CODE_CUR_USD);
            $result = $amount * $rate;
        } else {
            $result = $amount;
        }
        return $result;
    }

    public function exec($request) {
        assert($request instanceof WRequest);
        /** define local working data */
        $reqData = $request->getData();
        $amount = abs($reqData->getAmount());
        $currency = $reqData->getCurrency();
        $mlmId = $reqData->getCustomerMlmId();
        $notes = $reqData->getNotes();
        $odooRef = $reqData->getOdooRef();

        $reqData->get();
        $this->logger->info(self::class . " (req): amount=$amount, currency=$currency, mlmId=$mlmId, odooRef=$odooRef, notes=$notes.");

        $respRes = new WResult();
        $respData = new WData();

        /** perform processing */
        $amount = abs($amount);
        $respData->setOdooRef($odooRef);

        /* validate currency */
        if (!in_array($currency, $this->allowedCurs)) {
            $msg = "Given currency '$currency' is not allowed (" . json_encode($this->allowedCurs) . ").";
            $this->logger->error($msg);
            $respRes->setCode(WResponse::CODE_CURRENCY_UNKNOWN);
            $respRes->setText($msg);
        } else {
            $def = $this->manTrans->begin();
            try {
                /* prevent duplication */
                $found = $this->findDuplicates($odooRef);
                if ($found) {
                    $msg = "Odoo request referenced as '$odooRef' is already processed.";
                    $this->logger->error($msg);
                    $respRes->setCode(WResponse::CODE_DUPLICATED);
                    $respRes->setText($msg);
                } else {
                    /* validate customer existence */
                    $custId = $this->getCustomerId($mlmId);
                    if ($custId) {
                        $amountBase = $this->convertAmountToBaseCurrency($amount, $currency);
                        /* validate available balance */
                        $balance = $this->hlpAccBalance->get($custId, CfgWallet::CODE_TYPE_ASSET_WALLET);
                        if (($balance - $amountBase) > (0 - Cfg::DEF_ZERO)) { // >= 0
                            /* perform debit operation */
                            $operId = $this->performDebit($custId, $amountBase, $notes);
                            $this->registerOdooRequest($odooRef);
                            $respData->setOperationId($operId);
                            $respRes->setCode(WResponse::CODE_SUCCESS);
                        } else {
                            $respRes->setCode(WResponse::CODE_NOT_ENOUGH_BALANCE);
                            $msg = "Customer #$mlmId has '$balance' on the wallet balance. It's not enough to perform request ($amountBase).";
                            $this->logger->error($msg);
                            $respRes->setText($msg);
                        }
                    } else {
                        $respRes->setCode(WResponse::CODE_CUSTOMER_IS_NOT_FOUND);
                        $msg = "Customer #$mlmId is not found.";
                        $this->logger->error($msg);
                        $respRes->setText($msg);
                    }
                }
                $this->manTrans->commit($def);
            } finally {
                /* rollback uncommitted transactions on exception */
                $this->manTrans->end($def);
            }
        }

        /** compose result */
        $result = new WResponse();
        $result->setResult($respRes);
        $result->setData($respData);
        $this->logger->info(self::class . ' (res):' . var_export($respData, true));
        return $result;
    }

    /**
     * Look up for performed "Debit Customer Wallet" requests with the same Odoo Reference.
     *
     * @param string $odooRef
     * @return bool|\Praxigento\Odoo\Repo\Data\Registry\Request
     * @throws \Exception
     */
    private function findDuplicates($odooRef) {
        $entity = new ERegRequest();
        $entity->setTypeCode(HCodeReq::CUSTOMER_WALLET_DEBIT);
        $entity->setOdooRef($odooRef);
        $key = (array)$entity->get();
        $result = $this->daoRegRequest->getById($key);
        return $result;
    }

    /**
     * @param string $mlmId
     * @return int|null
     */
    private function getCustomerId($mlmId) {
        $result = null;
        $entity = $this->daoDwnlCust->getByMlmId($mlmId);
        if ($entity) {
            $result = $entity->getCustomerRef();
        }
        return $result;
    }

    /**
     * Prepare transaction data and perform debit operation.
     *
     * @param $custId
     * @param $amount
     * @param $note
     * @return int
     * @throws \Exception
     */
    private function performDebit($custId, $amount, $note) {
        /* get accounts */
        $assetTypeId = $this->daoTypeAsset->getIdByCode(Cfg::CODE_TYPE_ASSET_WALLET_ACTIVE);
        $debitAcc = $this->daoAcc->getByCustomerId($custId, $assetTypeId);
        $debitAccId = $debitAcc->getId();
        $creditAccId = $this->daoAcc->getSystemAccountId($assetTypeId);
        /* prepare transaction */
        $tran = new \Praxigento\Accounting\Repo\Data\Transaction();
        $tran->setDebitAccId($debitAccId);
        $tran->setCreditAccId($creditAccId);
        $tran->setValue($amount);
        $tran->setNote($note);
        /* perform operation */
        $req = new \Praxigento\Accounting\Api\Service\Operation\Create\Request();
        $req->setCustomerId($custId);
        $req->setOperationTypeCode(Cfg::CODE_TYPE_OPER_WALLET_DEBIT);
        $req->setTransactions([$tran]);
        $req->setOperationNote($note);
        $resp = $this->servOper->exec($req);
        $result = $resp->getOperationId();
        return $result;
    }

    /**
     * Register PV add request on Magento side to prevent double processing.
     *
     * @param string $odooRef
     * @throws \Exception
     */
    private function registerOdooRequest($odooRef) {
        $entity = new ERegRequest();
        $entity->setTypeCode(HCodeReq::CUSTOMER_WALLET_DEBIT);
        $entity->setOdooRef($odooRef);
        $this->daoRegRequest->create($entity);
    }
}
