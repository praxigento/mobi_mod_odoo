<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Sales\Order\PushRepeat;

use Praxigento\Odoo\Config as Cfg;

/**
 * Collect orders for Odoo push according to default conditions (still unreplicated).
 */
class Collector
{
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    protected $repoMageSalesOrder;
    /** @var \Praxigento\Odoo\Repo\Entity\ISaleOrder */
    protected $repoSaleOrder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $repoMageSalesOrder,
        \Praxigento\Odoo\Repo\Entity\ISaleOrder $repoSaleOrder
    ) {
        $this->repoMageSalesOrder = $repoMageSalesOrder;
        $this->repoSaleOrder = $repoSaleOrder;
    }

    /**
     * Select orders to be pushed into Odoo (in case of "on event" push was failed).
     */
    public function getOrdersToReplicate()
    {
        $result = [];
        $orders = $this->repoSaleOrder->getIdsToSaveToOdoo();
        foreach ($orders as $data) {
            $id = $data[Cfg::E_SALE_ORDER_A_ENTITY_ID];
            $order = $this->repoMageSalesOrder->get($id);
            $result[$id] = $order;
        }
        return $result;
    }
}