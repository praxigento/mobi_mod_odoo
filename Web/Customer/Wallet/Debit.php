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
use Praxigento\Odoo\Repo\Entity\Data\Registry\Request as ERegRequest;

/**
 * API adapter to internal services to transfer funds from customer wallet to system wallet.
 */
class Debit
    implements \Praxigento\Odoo\Api\Web\Customer\Wallet\DebitInterface
{

    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Accounting\Repo\Entity\Account */
    private $repoAcc;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;
    /** @var \Praxigento\Odoo\Repo\Entity\Registry\Request */
    private $repoRegRequest;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    private $repoTypeAsset;
    /** @var \Praxigento\Accounting\Api\Service\Operation */
    private $servOper;

    public function __construct(
        \Praxigento\Core\Api\App\Web\Authenticator\Rest $auth,
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Entity\Account $repoAcc,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset,
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust,
        \Praxigento\Odoo\Repo\Entity\Registry\Request $repoRegRequest,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Api\Service\Operation $servOper
    )
    {
        $this->logger = $logger;
        $this->repoAcc = $repoAcc;
        $this->repoTypeAsset = $repoTypeAsset;
        $this->repoDwnlCust = $repoDwnlCust;
        $this->repoRegRequest = $repoRegRequest;
        $this->manTrans = $manTrans;
        $this->servOper = $servOper;
    }

    public function exec($request)
    {
        assert($request instanceof WRequest);
        /** define local working data */
        $reqData = $request->getData();
        $amount = $reqData->getAmount();
        $mlmId = $reqData->getCustomerMlmId();
        $notes = $reqData->getNotes();
        $odooRef = $reqData->getOdooRef();

        $respResult = new WResult();
        $respData = new WData();

        /** perform processing */
        $amount = abs($amount);
        $respData->setOdooRef($odooRef);

        /* prevent duplication */
        $def = $this->manTrans->begin();
        try {
            $found = $this->findDuplicates($odooRef);
            if ($found) {
                $msg = "Odoo request referenced as '$odooRef' is already processed.";
                $this->logger->error($msg);
                $respResult->setCode(WResponse::CODE_DUPLICATED);
                $respResult->setText($msg);
            } else {
                $custId = $this->getCustomerId($mlmId);
                if ($custId) {
                    $operId = $this->performDebit($custId, $amount, $notes);
                    $this->registerOdooRequest($odooRef);
                    $respData->setOperationId($operId);
                    $respResult->setCode(WResponse::CODE_SUCCESS);
                } else {
                    $respResult->setCode(WResponse::CODE_CUSTOMER_IS_NOT_FOUND);
                    $msg = "Customer #$mlmId is not found.";
                    $this->logger->error($msg);
                    $respResult->setText($msg);
                }
            }
            $this->manTrans->commit($def);
        } finally {
            /* rollback uncommitted transactions on exception */
            $this->manTrans->end($def);
        }
        /** compose result */
        $result = new WResponse();
        $result->setResult($respResult);
        $result->setData($respData);
        return $result;
    }

    /**
     * Look up for performed "Debit Customer Wallet" requests with the same Odoo Reference.
     *
     * @param string $odooRef
     * @return bool|\Praxigento\Odoo\Repo\Entity\Data\Registry\Request
     * @throws \Exception
     */
    private function findDuplicates($odooRef)
    {
        $entity = new ERegRequest();
        $entity->setTypeCode(HCodeReq::CUSTOMER_WALLET_DEBIT);
        $entity->setOdooRef($odooRef);
        $key = (array)$entity->get();
        $result = $this->repoRegRequest->getById($key);
        return $result;
    }

    /**
     * @param string $mlmId
     * @return int|null
     */
    private function getCustomerId($mlmId)
    {
        $result = null;
        $entity = $this->repoDwnlCust->getByMlmId($mlmId);
        if ($entity) {
            $result = $entity->getCustomerId();
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
    private function performDebit($custId, $amount, $note)
    {
        /* get accounts */
        $assetTypeId = $this->repoTypeAsset->getIdByCode(Cfg::CODE_TYPE_ASSET_WALLET_ACTIVE);
        $debitAcc = $this->repoAcc->getByCustomerId($custId, $assetTypeId);
        $debitAccId = $debitAcc->getId();
        $creditAccId = $this->repoAcc->getRepresentativeAccountId($assetTypeId);
        /* prepare transaction */
        $tran = new \Praxigento\Accounting\Repo\Entity\Data\Transaction();
        $tran->setDebitAccId($debitAccId);
        $tran->setCreditAccId($creditAccId);
        $tran->setValue($amount);
        $tran->setNote($note);
        /* perform operation */
        $req = new \Praxigento\Accounting\Api\Service\Operation\Request();
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
    private function registerOdooRequest($odooRef)
    {
        $entity = new ERegRequest();
        $entity->setTypeCode(HCodeReq::CUSTOMER_WALLET_DEBIT);
        $entity->setOdooRef($odooRef);
        $this->repoRegRequest->create($entity);
    }
}