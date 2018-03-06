<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account\Pv;

/**
 * Operation for Odoo to add PV to the Magento customer.
 */
interface AddInterface
{
    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Pv\Add\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Pv\Add\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}