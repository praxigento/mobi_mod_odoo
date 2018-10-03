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


/**
 * API adapter for internal service to request accounting transactions data.
 */
class Transaction
    implements \Praxigento\Odoo\Api\Web\Account\TransactionInterface
{
    /** @var \Praxigento\Odoo\Web\Account\Transaction\A\GetItems */
    private $aGetItems;
    /** @var \Praxigento\Odoo\Web\Account\Transaction\A\GetBalances */
    private $aGetBalances;

    public function __construct(
        \Praxigento\Odoo\Web\Account\Transaction\A\GetBalances $aGetBalances,
        \Praxigento\Odoo\Web\Account\Transaction\A\GetItems $aGetItems
    )
    {
        $this->aGetBalances = $aGetBalances;
        $this->aGetItems = $aGetItems;
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

        /** perform processing */
        $items = $this->aGetItems->exec($assetTypeCode, $customerMlmId, $dateFrom, $dateTo);
        list($open, $close) = $this->aGetBalances->exec($assetTypeCode, $customerMlmId, $dateFrom, $dateTo);

        /** compose result */
        $respData = new WData();
        $respData->setItems($items);
        $respData->setBalanceOpen($open);
        $respData->setBalanceClose($close);

        $respRes = new WResult();
        $respRes->setCode(WResponse::CODE_SUCCESS);

        $result = new WResponse();
        $result->setData($respData);
        $result->setResult($respRes);
        return $result;
    }

}