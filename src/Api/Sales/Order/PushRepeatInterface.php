<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Sales\Order;

/**
 * Request sale orders data push (from Magento to Odoo).
 *
 * @api
 */
interface PushRepeatInterface
{

    /**
     * Command to request sale orders data push (from Magento to Odoo).
     *
     * @return \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report
     */
    public function execute();
}