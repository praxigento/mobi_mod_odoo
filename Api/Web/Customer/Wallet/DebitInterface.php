<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Customer\Wallet;

/**
 * Transfer funds from customer wallet to system wallet.
 */
interface DebitInterface
{
    /**
     * Transfer funds from customer wallet to system wallet.
     *
     * @param \Praxigento\Odoo\Api\Web\Customer\Wallet\Debit\Request $request
     * @return \Praxigento\Odoo\Api\Web\Customer\Wallet\Debit\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}