<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account;

use Praxigento\Core\Api\App\Web\Response\Result as WResult;
use Praxigento\Odoo\Api\Web\Account\Balances\Request as WRequest;
use Praxigento\Odoo\Api\Web\Account\Balances\Response as WResponse;
use Praxigento\Odoo\Api\Web\Account\Balances\Response\Data as WData;


/**
 * API adapter for internal service to request customers balances for period if dates.
 */
class Balances
    implements \Praxigento\Odoo\Api\Web\Account\BalancesInterface
{
    /** @var \Praxigento\Odoo\Web\Account\Balances\A\GetBalances */
    private $aGetBalances;

    public function __construct(
        \Praxigento\Odoo\Web\Account\Balances\A\GetBalances $aGetBalances
    ) {
        $this->aGetBalances = $aGetBalances;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Balances\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Balances\Response
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof WRequest);
        /** define local working data */
        $reqData = $request->getData();
        $assetTypeCode = $reqData->getAssetTypeCode();
        $customers = $reqData->getCustomers();
        $dateFrom = $reqData->getDateFrom();
        $dateTo = $reqData->getDateTo();

        /** perform processing */
        $items = $this->aGetBalances->exec($assetTypeCode, $customers, $dateFrom, $dateTo);

        /** compose result */
        $respData = new WData();
        $respData->setItems($items);

        $respRes = new WResult();
        $respRes->setCode(WResponse::CODE_SUCCESS);

        $result = new WResponse();
        $result->setData($respData);
        $result->setResult($respRes);
        return $result;
    }

}