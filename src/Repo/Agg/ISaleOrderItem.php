<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg;

/**
 * DS-repo to get data to replicate Sale Order Items to Odoo.
 */
interface ISaleOrderItem extends \Praxigento\Core\Repo\IBaseDataSource
{
    public function getByOrderAndStock($orderId, $stockId);
}