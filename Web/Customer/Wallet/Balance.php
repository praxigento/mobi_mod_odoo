<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Customer\Wallet;

use Praxigento\Core\Api\App\Web\Response\Result as WResult;
use Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Request as WRequest;
use Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Response as WResponse;
use Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Response\Data as WData;
use Praxigento\Odoo\Config as Cfg;

/**
 * API adapter to internal services to get balance for customer wallet.
 */
class Balance
    implements \Praxigento\Odoo\Api\Web\Customer\Wallet\BalanceInterface
{
    /** @var \Praxigento\Downline\Repo\Entity\Customer */
    private $repoDwnlCust;
    /** @var \Praxigento\Accounting\Api\Service\Account\Get */
    private $servAccGet;

    public function __construct(
        \Praxigento\Downline\Repo\Entity\Customer $repoDwnlCust,
        \Praxigento\Accounting\Api\Service\Account\Get $servAccGet
    ) {
        $this->repoDwnlCust = $repoDwnlCust;
        $this->servAccGet = $servAccGet;
    }

    public function exec($request)
    {
        assert($request instanceof WRequest);
        /** define local working data */
        $reqData = $request->getData();
        $mlmId = $reqData->getCustomerMlmId();
        $respResult = new WResult();
        $respData = new WData();

        /** perform processing */
        $custId = $this->getCustomerId($mlmId);
        if ($custId) {
            $balance = $this->getBalance($custId);
            $respData->setBalance($balance);
            $respResult->setCode(WResponse::CODE_SUCCESS);
        } else {
            $respResult->setCode(WResponse::CODE_CUSTOMER_IS_NOT_FOUND);
        }
        /** compose result */
        $result = new WResponse();
        $result->setResult($respResult);
        $result->setData($respData);
        return $result;
    }

    /**
     * @param int $custId
     * @return float
     * @throws \Exception
     */
    private function getBalance($custId)
    {
        $req = new \Praxigento\Accounting\Api\Service\Account\Get\Request();
        $req->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_WALLET_ACTIVE);
        $req->setCustomerId($custId);
        $resp = $this->servAccGet->exec($req);
        $result = (float)$resp->getBalance();
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
}