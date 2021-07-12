<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Customer\Pv;

use Praxigento\Core\Api\App\Web\Response\Result as WResult;
use Praxigento\Odoo\Api\Web\Customer\Pv\Add\Request as WRequest;
use Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response as WResponse;
use Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response\Data as WData;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Helper\Code\Request as HCodeReq;
use Praxigento\Odoo\Repo\Data\Registry\Request as ERegRequest;

/**
 * API adapter for internal service to add PV to the Magento customer (Odoo replication).
 */
class Add
    implements \Praxigento\Odoo\Api\Web\Customer\Pv\AddInterface
{
    /** @var \Praxigento\Core\Api\App\Web\Authenticator\Rest */
    private $auth;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Odoo\Repo\Dao\Registry\Request */
    private $daoRegRequest;
    /** @var \Praxigento\Accounting\Repo\Dao\Type\Asset */
    private $daoTypeAsset;
    /** @var \Praxigento\Downline\Api\Helper\Config */
    private $hlpCfgDwnl;
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $repoCust;
    /** @var \Praxigento\Accounting\Api\Service\Account\Asset\Transfer */
    private $servAssetTransfer;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCust,
        \Praxigento\Core\Api\App\Web\Authenticator\Rest $auth,
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Dao\Type\Asset $daoTypeAsset,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Odoo\Repo\Dao\Registry\Request $daoRegRequest,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Api\Service\Account\Asset\Transfer $servAssetTransfer,
        \Praxigento\Downline\Api\Helper\Config $hlpCfgDwnl
    ) {
        $this->repoCust = $repoCust;
        $this->auth = $auth;
        $this->logger = $logger;
        $this->daoTypeAsset = $daoTypeAsset;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->daoRegRequest = $daoRegRequest;
        $this->manTrans = $manTrans;
        $this->servAssetTransfer = $servAssetTransfer;
        $this->hlpCfgDwnl = $hlpCfgDwnl;
    }

    public function exec($request)
    {
        assert($request instanceof \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Request);
        /** define local working data */
        $data = $request->getData();
        $dateApplied = $data->getDateApplied();
        $mlmId = $data->getCustomerMlmId();
        $notes = $data->getNotes();
        $odooRef = $data->getOdooRef();
        $pv = $data->getPv();

        $respRes = new WResult();
        $respData = new WData();

        /** perform processing */
        $respData->setOdooRef($odooRef);
        $amount = abs($pv);
        $assetId = $this->getAssetId();
        $custId = $this->getCustomerId($mlmId);
        $userId = $this->getUserId($request);

        /* add PV to customer account */
        $req = new \Praxigento\Accounting\Api\Service\Account\Asset\Transfer\Request();
        $req->setAmount($amount);
        $req->setAssetId($assetId);
        $req->setCustomerId($custId);
        $req->setDateApplied($dateApplied);
        $req->setIsDirect(true);
        $req->setNote($notes);
        $req->setUserId($userId);

        // repeat 3 attempts to execute operation with 1 sec. await interval between attempts
        $MAX = 3;
        $WAIT = 1000;
        $count = 1;
        $done = false;
        do {
            try {
                $this->attempts($respRes, $respData, $req, $odooRef, $mlmId);
                $done = true;
            } catch (\Throwable $e) {
                $msg = $e->getMessage();
                $this->logger->error("Cannot add PV to customer: $msg");
                if (str_contains($msg, 'Deadlock found when trying to get lock')) {
                    if ($count < $MAX) {
                        // wait and increase counter
                        usleep($WAIT);
                        $count++;
                        $this->logger->error("One more attempt will be given ($count of $MAX).");
                    } else {
                        $done = true;
                        $respRes->setText($msg);
                        $this->logger->error("All attempts are off ($count of $MAX).");
                    }
                } else {
                    $done = true;
                    $respRes->setText($msg);
                }
            }
        } while (!$done);

        /** compose result */
        $result = new WResponse();
        $result->setResult($respRes);
        $result->setData($respData);
        return $result;
    }

    /**
     * This method is extracted from 'exec()' and is not well done but it is done quick.
     *
     * @param \Praxigento\Core\Api\App\Web\Response\Result $respRes
     * @param \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response\Data $respData
     * @param \Praxigento\Accounting\Api\Service\Account\Asset\Transfer\Request $req
     * @param $odooRef
     * @param $mlmId
     * @throws \Exception
     */
    private function attempts(
        \Praxigento\Core\Api\App\Web\Response\Result $respRes,
        \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response\Data $respData,
        \Praxigento\Accounting\Api\Service\Account\Asset\Transfer\Request $req,
        $odooRef,
        $mlmId
    ) {
        $custId = $req->getCustomerId();
        $pv = $req->getAmount();

        /* prevent duplication */
        $def = $this->manTrans->begin();
        try {
            $found = $this->findDuplicates($odooRef);
            if ($found) {
                $msg = "Odoo request referenced as '$odooRef' is already processed.";
                $this->logger->error($msg);
                $respRes->setCode(WResponse::CODE_DUPLICATED);
                $respRes->setText($msg);
            } else {
                /* validate customer group */
                $isValidGroup = $this->isValidGroup($custId);
                if ($isValidGroup) {
                    $resp = $this->servAssetTransfer->exec($req);
                    $operId = $resp->getOperId();

                    if ($operId) {
                        $this->registerOdooRequest($odooRef);
                        /* compose response */
                        $respData->setOperationId($operId);
                        $respRes->setCode(WResponse::CODE_SUCCESS);
                        $msg = "$pv PV are credited to customer #$mlmId (odoo ref. #$odooRef).";
                        $this->logger->info($msg);
                    } else {
                        $respRes->setText($resp->getErrorMessage());
                    }
                } else {
                    $msg = "Customer #$mlmId/$custId has group that is not allowed for PV transfers.";
                    $this->logger->error($msg);
                    $respRes->setCode(WResponse::CODE_WRONG_CUST_GROUP);
                    $respRes->setText($msg);
                }
            }
            $this->manTrans->commit($def);
        } finally {
            /* rollback uncommitted transactions on exception */
            $this->manTrans->end($def);
        }
    }

    /**
     * Look up for performed "Add PV to Customer" requests with the same Odoo Reference.
     *
     * @param string $odooRef
     * @return bool|\Praxigento\Odoo\Repo\Data\Registry\Request
     * @throws \Exception
     */
    private function findDuplicates($odooRef) {
        $entity = new ERegRequest();
        $entity->setTypeCode(HCodeReq::CUSTOMER_PV_ADD);
        $entity->setOdooRef($odooRef);
        $key = (array)$entity->get();
        $result = $this->daoRegRequest->getById($key);
        return $result;
    }

    /** @return int */
    private function getAssetId()
    {
        $result = $this->daoTypeAsset->getIdByCode(Cfg::CODE_TYPE_ASSET_PV);
        return $result;
    }

    /**
     * @param string $mlmId
     * @return int
     * @throws \Exception
     */
    private function getCustomerId($mlmId)
    {
        $customer = $this->daoDwnlCust->getByMlmId($mlmId);
        if (!$customer) {
            throw new \Exception("Cannot find customer with MLM ID: $mlmId.");
        }
        $result = $customer->getCustomerRef();
        return $result;
    }

    /**
     * Get admin user to log in operations log.
     *
     * @param WRequest $request
     * @return mixed
     */
    private function getUserId($request)
    {
        $result = $this->auth->getCurrentUserId($request);
        return $result;
    }

    private function isValidGroup($custId)
    {
        $result = false;
        $found = $this->repoCust->getById($custId);
        if ($found) {
            $groupId = $found->getGroupId();
            $allowedGroups = $this->hlpCfgDwnl->getDowngradeGroupsDistrs();
            $result = in_array($groupId, $allowedGroups);
        }
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
        $entity->setTypeCode(HCodeReq::CUSTOMER_PV_ADD);
        $entity->setOdooRef($odooRef);
        $this->daoRegRequest->create($entity);
    }
}
