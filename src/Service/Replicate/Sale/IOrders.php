<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;

/**
 * Push replication (Mage2Odoo) for sale orders.
 */
interface IOrders
{
    /**
     * @param \Praxigento\Odoo\Service\Replicate\Sale\Orders\Request $req
     * @return \Praxigento\Odoo\Service\Replicate\Sale\Orders\Response
     */
    public function exec(\Praxigento\Odoo\Service\Replicate\Sale\Orders\Request $req);
}