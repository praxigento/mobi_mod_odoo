<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Helper\Code;

/**
 * Odoo requests types codifier (see \Praxigento\Odoo\Repo\Data\Registry\Request::ATTR_TYPE_CODE).
 */
class Request
{
    /**
     * See \Praxigento\Odoo\Api\Web\Customer\Pv\Add command.
     */
    const CUSTOMER_PV_ADD = 100;
    const CUSTOMER_WALLET_DEBIT = 200;
}