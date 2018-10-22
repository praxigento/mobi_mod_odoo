<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account;

/**
 * Request saldo for filtered transactions (by operation type, customers, actives).
 */
interface SaldoInterface
{
    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Saldo\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Saldo\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}