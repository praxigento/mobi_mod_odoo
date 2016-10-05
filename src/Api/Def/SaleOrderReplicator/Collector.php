<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Def\SaleOrderReplicator;

use Praxigento\Odoo\Config as Cfg;

/**
 * Collect orders according to conditions.
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collector
{
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    protected $_repoMageSalesOrder;
    /** @var \Praxigento\Odoo\Repo\Entity\ISaleOrder */
    protected $_repoSaleOrder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $repoMageSalesOrder,
        \Praxigento\Odoo\Repo\Entity\ISaleOrder $repoSaleOrder
    ) {
        $this->_repoMageSalesOrder = $repoMageSalesOrder;
        $this->_repoSaleOrder = $repoSaleOrder;
    }

    /**
     * Select orders to be pushed into Odoo (in case of "on event" push was failed).
     */
    public function getOrdersToReplicate()
    {
        $result = [];
        $orders = $this->_repoSaleOrder->getIdsToSaveToOdoo();
        foreach ($orders as $data) {
            $id = $data[Cfg::E_SALE_ORDER_A_ENTITY_ID];
            $order = $this->_repoMageSalesOrder->get($id);
            $result[$id] = $order;
        }
        return $result;
    }
}