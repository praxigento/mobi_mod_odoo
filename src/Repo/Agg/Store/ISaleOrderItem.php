<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Store;

/**
 * DS-repo to get data to replicate Sale Order Items to Odoo.
 *
 * @deprecated
 * @see \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Get\Builder
 */
interface ISaleOrderItem extends \Praxigento\Core\Repo\IDataSource
{
    /**
     * @param int $orderId
     * @param int $stockId
     * @return \Praxigento\Odoo\Repo\Agg\Data\SaleOrderItem[]
     */
    public function getByOrderAndStock($orderId, $stockId);
}