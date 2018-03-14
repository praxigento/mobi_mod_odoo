<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Customer\Wallet;

/**
 * Get balance for customer wallet.
 */
interface BalanceInterface
{
    /**
     * Get balance for customer wallet.
     *
     * @param \Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Request $request
     * @return \Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}