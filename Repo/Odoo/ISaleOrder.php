<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo;

interface ISaleOrder
{
    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder $order
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Response|\Praxigento\Odoo\Repo\Odoo\Data\Error
     */
    public function save($order);
}