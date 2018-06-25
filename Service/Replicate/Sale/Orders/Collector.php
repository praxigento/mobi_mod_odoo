<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale\Orders;

use Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get\Builder as QBGetOrders;

/**
 * Collect orders for Odoo push according to default conditions (still not replicated).
 */
class Collector
{
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    private $daoMageSalesOrder;
    /** @var \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get\Builder */
    private $qGetOrders;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $daoMageSalesOrder,
        \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get\Builder $qGetOrders
    ) {
        $this->daoMageSalesOrder = $daoMageSalesOrder;
        $this->qGetOrders = $qGetOrders;
    }

    /**
     * Select orders to be pushed into Odoo (in case of "on event" push was failed).
     */
    public function getOrdersToReplicate()
    {
        $result = [];
        $query = $this->qGetOrders->build();
        $conn = $query->getConnection();
        $orders = $conn->fetchAll($query);
        foreach ($orders as $data) {
            $id = $data[QBGetOrders::A_ORDER_ID];
            $order = $this->daoMageSalesOrder->get($id);
            $result[$id] = $order;
        }
        return $result;
    }
}