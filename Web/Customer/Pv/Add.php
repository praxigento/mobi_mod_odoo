<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Customer\Pv;

use Praxigento\Odoo\Api\Web\Customer\Pv\Add\Request as ARequest;
use Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response as AResponse;
use Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response\Data as AData;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Helper\Code\Request as HCodeReq;
use Praxigento\Odoo\Repo\Entity\Data\Registry\Request as ERegRequest;

/**
 * API adapter for internal service to add PV to the Magento customer (Odoo replication).
 */
class Add
    implements \Praxigento\Odoo\Api\Web\Customer\Pv\AddInterface
{
    /** @var \Praxigento\Core\Api\App\Web\Authenticator\Rest */
    private $auth;
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;
    /** @var \Praxigento\Odoo\Repo\Entity\Registry\Request */
    private $repoRegRequest;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    private $repoTypeAsset;
    /** @var \Praxigento\Accounting\Service\Account\Asset\Transfer */
    private $servAssetTransfer;

    public function __construct(
        \Praxigento\Core\Api\App\Web\Authenticator\Rest $auth,
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset,
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust,
        \Praxigento\Odoo\Repo\Entity\Registry\Request $repoRegRequest,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Accounting\Service\Account\Asset\Transfer $servAssetTransfer
    ) {
        $this->auth = $auth;
        $this->logger = $logger;
        $this->repoTypeAsset = $repoTypeAsset;
        $this->repoDwnlCust = $repoDwnlCust;
        $this->repoRegRequest = $repoRegRequest;
        $this->manTrans = $manTrans;
        $this->servAssetTransfer = $servAssetTransfer;
    }

    public function exec($request)
    {
        assert($request instanceof \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Request);
        /** define local working data */
        $data = $request->getData();
        $mlmId = $data->getCustomerMlmId();
        $notes = $data->getNotes();
        $odooRef = $data->getOdooRef();
        $pv = $data->getPv();

        /** perform processing */
        $amount = abs($pv);
        $assetId = $this->getAssetId();
        $custId = $this->getCustomerId($mlmId);
        $userId = $this->getUserId($request);

        $baseResult = new \Praxigento\Core\Api\App\Web\Response\Result();
        $dataResp = new AData();

        /* prevent duplication */
        $def = $this->manTrans->begin();
        try {
            $found = $this->findDuplicates($odooRef);
            if ($found) {
                $msg = "Odoo request referenced as '$odooRef' is already processed.";
                $this->logger->error($msg);
                $baseResult->setCode(AResponse::CODE_DUPLICATED);
                $baseResult->setText($msg);
            } else {
                /* add PV to customer account */
                $req = new \Praxigento\Accounting\Service\Account\Asset\Transfer\Request();
                $req->setAmount($amount);
                $req->setAssetId($assetId);
                $req->setCustomerId($custId);
                $req->setUserId($userId);
                $req->setIsDirect(true);
                $req->setNote($notes);

                $resp = $this->servAssetTransfer->exec($req);
                $operId = $resp->getOperId();

                if ($operId) {
                    $this->registerOdooRequest($odooRef);
                    /* compose response */
                    $dataResp->setOdooRef($odooRef);
                    $dataResp->setOperationId($operId);
                    $baseResult->setCode(AResponse::CODE_SUCCESS);
                    $msg = "$pv PV are credited to customer #$mlmId (odoo ref. #$odooRef).";
                    $this->logger->info($msg);
                }
            }
            $this->manTrans->commit($def);
        } finally {
            /* rollback uncommitted transactions on exception */
            $this->manTrans->end($def);
        }
        /** compose result */
        $result = new \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response();
        $result->setResult($baseResult);
        $result->setData($dataResp);
        return $result;
    }

    /**
     * Look up for performed "Add PV to Customer" requests with the same Odoo Reference.
     *
     * @param string $odooRef
     * @return bool|\Praxigento\Odoo\Repo\Entity\Data\Registry\Request
     * @throws \Exception
     */
    private function findDuplicates($odooRef)
    {
        $entity = new ERegRequest();
        $entity->setTypeCode(HCodeReq::CUSTOMER_PV_ADD);
        $entity->setOdooRef($odooRef);
        $key = (array)$entity->get();
        $result = $this->repoRegRequest->getById($key);
        return $result;
    }

    /** @return int */
    private function getAssetId()
    {
        $result = $this->repoTypeAsset->getIdByCode(Cfg::CODE_TYPE_ASSET_PV);
        return $result;
    }

    /**
     * @param string $mlmId
     * @return int
     * @throws \Exception
     */
    private function getCustomerId($mlmId)
    {
        $customer = $this->repoDwnlCust->getByMlmId($mlmId);
        if (!$customer) {
            throw new \Exception("Cannot find customer with MLM ID: $mlmId.");
        }
        $result = $customer->getCustomerId();
        return $result;
    }

    /**
     * Get admin user to log in operations log.
     *
     * @param ARequest $request
     * @return mixed
     */
    private function getUserId($request)
    {
        $result = $this->auth->getCurrentUserId($request);
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
        $this->repoRegRequest->create($entity);
    }
}