<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Customer\Pv;

/**
 * Add PV to customer account.
 *
 * @api
 */
interface AddInterface
{
    /**
     * Command to add PV to customer account.
     *
     * @param \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Request $request
     * @return \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}