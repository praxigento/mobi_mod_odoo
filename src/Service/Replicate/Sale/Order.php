<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;


class Order
    extends \Praxigento\Core\Service\Base\Call
    implements \Praxigento\Odoo\Service\Replicate\Sale\IOrder
{
    /** @var \Praxigento\Odoo\Repo\Entity\ISaleOrder */
    protected $repoEntitySaleOrder;
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\Order\Collector */
    protected $subCollector;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Repo\Entity\ISaleOrder $repoEntitySaleOrder,
        \Praxigento\Odoo\Service\Replicate\Sale\Order\Collector $collector
    )
    {
        parent::__construct($logger, $manObj);
        $this->repoEntitySaleOrder = $repoEntitySaleOrder;
        $this->subCollector = $collector;
    }

    public function exec(\Praxigento\Odoo\Service\Replicate\Sale\Order\Request $req)
    {
        $result = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Response();
        /** @var \Magento\Sales\Api\Data\OrderInterface $mageOrder */
        $mageOrder = $req->getSaleOrder();
        $orderIdMage = $mageOrder->getEntityId();
        $customerIdMage = $mageOrder->getCustomerId();
        /** @var \Praxigento\Odoo\Data\Entity\SaleOrder $registeredOrder */
        $registeredOrder = $this->repoEntitySaleOrder->getById($orderIdMage);
        $isRegistered = (bool)$registeredOrder;
        /* TODO: remove reverted value */
        $isRegistered = !$isRegistered;
        /* skip processing for registered orders or guest checkouted */
        if ($orderIdMage && !$isRegistered && $customerIdMage) {
            $odooOrder = $this->subCollector->getSaleOrder($mageOrder);
            /* save order into Odoo repo */

        } else {
            $msg = "Order replication to Odoo is skipped (id/is_registered/customer_id): $orderIdMage/"
                . (string)$isRegistered . "/$customerIdMage.";
            $this->logger->info($msg);
        }
        return $result;
    }

}