<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Odoo\Api\Web\Customer\Get;

/**
 * Get list of all customers.
 */
interface AllInterface
{
    /**
     * @param \Praxigento\Odoo\Api\Web\Customer\Get\All\Request $request
     * @return \Praxigento\Odoo\Api\Web\Customer\Get\All\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}