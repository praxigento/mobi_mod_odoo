<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account;

/**
 * Request accounting transactions data.
 */
interface TransactionInterface
{
    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Transaction\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Transaction\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}