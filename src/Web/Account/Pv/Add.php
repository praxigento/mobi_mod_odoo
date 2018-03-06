<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Account;

use Praxigento\Odoo\Api\Web\Account\Pv\Add\Request as ARequest;
use Praxigento\Odoo\Api\Web\Account\Pv\Add\Response as AResponse;

/**
 * API adapter for internal service to add PV to the Magento customer (Odoo replication).
 */
class Add
    implements \Praxigento\Odoo\Api\Web\Account\Pv\AddInterface
{


    public function __construct()
    {
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */

        /** perform processing */

        /** compose result */
        $result = new AResponse();
        return $result;
    }
}