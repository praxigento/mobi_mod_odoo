<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Customer\Wallet;

use Praxigento\Odoo\Api\Web\Customer\Wallet\Debit\Request as ARequest;
use Praxigento\Odoo\Api\Web\Customer\Wallet\Debit\Response as AResponse;
use Praxigento\Odoo\Api\Web\Customer\Wallet\Debit\Response\Data as AData;

/**
 * API adapter to internal services to transfer funds from customer wallet to system wallet.
 */
class Debit
    implements \Praxigento\Odoo\Api\Web\Customer\Wallet\DebitInterface
{


    public function __construct()
    {
    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $reqData = $request->getData();

        /** perform processing */
        $respData = new AData();

        /** compose result */
        $result = new AResponse();
        $result->setData($respData);
        $result->getResult()->setCode(AResponse::CODE_SUCCESS);
        return $result;
    }
}