<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account;

use Praxigento\Core\Api\App\Web\Response\Result as WResult;
use Praxigento\Odoo\Api\Web\Account\Transaction\Request as WRequest;
use Praxigento\Odoo\Api\Web\Account\Transaction\Response as WResponse;
use Praxigento\Odoo\Api\Web\Account\Transaction\Response\Data as WData;
use Praxigento\Odoo\Config as Cfg;


/**
 * API adapter for internal service to request accounting transactions data.
 */
class Transaction
    implements \Praxigento\Odoo\Api\Web\Account\TransactionInterface
{

    public function __construct()
    {
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Transaction\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Transaction\Response
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof WRequest);
        /** define local working data */
        $reqData = $request->getData();
        $assetTypeCode = $reqData->getAssetTypeCode();
        $customerMlmId = $reqData->getCustomerMlmId();
        $dateFrom = $reqData->getDateFrom();
        $dateTo = $reqData->getDateTo();

        $respRes = new WResult();
        $respData = new WData();
        $cfg = Cfg::CODE_TYPE_ASSET_PV;

        /** perform processing */
        $respRes->setCode(WResponse::CODE_SUCCESS);

        /** compose result */
        $result = new WResponse();
        $result->setResult($respRes);
        $result->setData($respData);
        return $result;
    }

}