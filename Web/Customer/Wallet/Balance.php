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
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCust;
    /** @var \Praxigento\Core\Api\Helper\Customer\Currency */
    private $hlpCustCur;
    /** @var \Praxigento\Accounting\Api\Service\Account\Get */
    private $servAccGet;

    public function __construct(
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Core\Api\Helper\Customer\Currency $hlpCustCur,
        \Praxigento\Accounting\Api\Service\Account\Get $servAccGet
    ) {
        $this->daoDwnlCust = $daoDwnlCust;
        $this->hlpCustCur = $hlpCustCur;
        $this->servAccGet = $servAccGet;
    }

    public function exec($request)
    {
        assert($request instanceof WRequest);
        /** define local working data */
        $data = $request->getData();
        $mlmId = $data->getCustomerMlmId();

        $respRes = new WResult();
        $respData = new WData();

        /** perform processing */
        $custId = $this->getCustomerId($mlmId);
        if ($custId) {
            $balance = $this->getBalance($custId);
            $balance = $this->hlpCustCur->convertFromBase($balance, $custId);
            $cur = $this->hlpCustCur->getCurrency($custId);
            $respData->setBalance($balance);
            $respData->setCurrency($cur);
            $respRes->setCode(WResponse::CODE_SUCCESS);
        } else {
            $respRes->setCode(WResponse::CODE_CUSTOMER_IS_NOT_FOUND);
        }

        /** compose result */
        $result = new WResponse();
        $result->setResult($respRes);
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
        $entity = $this->daoDwnlCust->getByMlmId($mlmId);
        if ($entity) {
            $result = $entity->getCustomerId();
        }
        return $result;
    }
}