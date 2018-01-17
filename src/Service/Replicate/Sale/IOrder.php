<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;

/**
 * Replicate one order to Odoo.
 *
 * This service is refactoring of the \Praxigento\Odoo\Service\IReplicate::orderSave code.
 * TODO: this is module level service, interface is redundant.
 */
interface IOrder
{
    /**
     * @param \Praxigento\Odoo\Service\Replicate\Sale\Order\Request $req
     * @return \Praxigento\Odoo\Service\Replicate\Sale\Order\Response
     */
    public function exec(\Praxigento\Odoo\Service\Replicate\Sale\Order\Request $req);
}