<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Helper\Replicate\Orders;

use Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get\Builder as QBGetOrders;

/**
 * Collect orders for Odoo push replication according to default conditions (still not replicated).
 */
class Collector
{
    /** @var \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get\Builder */
    private $qOrdersGet;
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    private $repoSalesOrder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $repoSalesOrder,
        \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get\Builder $qOrdersGet
    ) {
        $this->repoSalesOrder = $repoSalesOrder;
        $this->qOrdersGet = $qOrdersGet;
    }

    /**
     * Select orders to be pushed into Odoo (in case of "on event" push was failed).
     * @return \Magento\Sales\Api\Data\OrderInterface[]
     */
    public function getOrdersToReplicate()
    {
        $result = [];
        $query = $this->qOrdersGet->build();
        $conn = $query->getConnection();
        $orders = $conn->fetchAll($query);
        foreach ($orders as $data) {
            $id = $data[QBGetOrders::A_ORDER_ID];
            $order = $this->repoSalesOrder->get($id);
            $result[$id] = $order;
        }
        return $result;
    }
}