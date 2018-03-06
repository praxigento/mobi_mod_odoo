<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Account\Pv;

use Praxigento\Odoo\Api\Web\Account\Pv\Add\Request as ARequest;
use Praxigento\Odoo\Api\Web\Account\Pv\Add\Response as AResponse;
use Praxigento\Odoo\Config as Cfg;

/**
 * API adapter for internal service to add PV to the Magento customer (Odoo replication).
 */
class Add
    implements \Praxigento\Odoo\Api\Web\Account\Pv\AddInterface
{
    /** @var \Praxigento\Core\App\Api\Web\Authenticator\Back */
    private $auth;
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;
    /** @var \Praxigento\Accounting\Service\Account\Asset\Transfer */
    private $servAssetTransfer;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    private $repoTypeAsset;

    public function __construct(
        \Praxigento\Core\App\Api\Web\Authenticator\Back $auth,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset,
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust,
        \Praxigento\Accounting\Service\Account\Asset\Transfer $servAssetTransfer
    )
    {
        $this->auth = $auth;
        $this->repoTypeAsset = $repoTypeAsset;
        $this->repoDwnlCust = $repoDwnlCust;
        $this->servAssetTransfer = $servAssetTransfer;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Pv\Add\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Pv\Add\Response
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $mlmId = $data->getMlmId();
        $amount = $data->getAmount();
        $notes = $data->getNotes();

        /** perform processing */
        $amount = abs($amount);
        $assetId = $this->getAssetId();
        $custId = $this->getCustomerId($mlmId);
        $userId = $this->getUserId($request);

        $req = new \Praxigento\Accounting\Service\Account\Asset\Transfer\Request();
        $req->setAmount($amount);
        $req->setAssetId($assetId);
        $req->setCustomerId($custId);
        $req->setUserId($userId);
        $req->setIsDirect(true);
        $req->setNote($notes);

        $resp = $this->servAssetTransfer->exec($req);
        $operId = $resp->getOperId();

        /** compose result */
        $result = new AResponse();
        $respData = new \Praxigento\Odoo\Api\Web\Account\Pv\Add\Response\Data();
        $respData->setOperationId($operId);
        $result->setData($respData);
        return $result;
    }

    /**
     * @return int|null
     */
    private function getAssetId()
    {
        $result = $this->repoTypeAsset->getIdByCode(Cfg::CODE_TYPE_ASSET_PV);
        return $result;
    }

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
}