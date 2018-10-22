<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account;

use Praxigento\Core\Api\App\Web\Response\Result as WResult;
use Praxigento\Odoo\Api\Web\Account\Saldo\Request as WRequest;
use Praxigento\Odoo\Api\Web\Account\Saldo\Response as WResponse;
use Praxigento\Odoo\Api\Web\Account\Saldo\Response\Data as WData;


/**
 * Web service implementation to get saldo for filtered transactions.
 */
class Saldo
    implements \Praxigento\Odoo\Api\Web\Account\SaldoInterface
{

    public function __construct()
    {

    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Saldo\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Saldo\Response
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof WRequest);
        /** define local working data */
        $reqData = $request->getData();
        $operTypes = $reqData->getOperTypes();
        $customers = $reqData->getCustomers();
        $dateFrom = $reqData->getDateFrom();
        $dateTo = $reqData->getDateTo();

        /** perform processing */

        /** compose result */
        $respData = new WData();

        $respRes = new WResult();
        $respRes->setCode(WResponse::CODE_SUCCESS);

        $result = new WResponse();
        $result->setData($respData);
        $result->setResult($respRes);
        return $result;
    }

}