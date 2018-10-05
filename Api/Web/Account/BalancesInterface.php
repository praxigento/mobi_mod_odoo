<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account;

/**
 * Get balances for list of customers for period.
 */
interface BalancesInterface
{
    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Balances\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Balances\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}