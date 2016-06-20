<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo;

interface ISaleOrder
{
    /**
     * @param \Praxigento\Odoo\Data\Odoo\SaleOrder $order
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\PostResponse
     */
    public function save($order);
}