<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;

class Order
    implements \Praxigento\Odoo\Service\Replicate\Sale\IOrder
{
    /** @var \Praxigento\Odoo\Repo\Dao\SaleOrder */
    private $daoEntitySaleOrder;
    /** @var \Praxigento\Odoo\Repo\Odoo\ISaleOrder */
    private $daoOdooSaleOrder;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\Order\Collector */
    private $subCollector;

    public function __construct(
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Odoo\Repo\Dao\SaleOrder $daoEntitySaleOrder,
        \Praxigento\Odoo\Repo\Odoo\ISaleOrder $daoOdooSaleOrder,
        \Praxigento\Odoo\Service\Replicate\Sale\Order\Collector $collector
    ) {
        $this->logger = $logger;
        $this->daoEntitySaleOrder = $daoEntitySaleOrder;
        $this->daoOdooSaleOrder = $daoOdooSaleOrder;
        $this->subCollector = $collector;
    }

    public function exec(\Praxigento\Odoo\Service\Replicate\Sale\Order\Request $req)
    {
        $result = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Response();
        /** @var \Magento\Sales\Api\Data\OrderInterface $mageOrder */
        $mageOrder = $req->getSaleOrder();
        $orderIdMage = $mageOrder->getEntityId();
        $customerIdMage = $mageOrder->getCustomerId();
        /** @var \Praxigento\Odoo\Repo\Data\SaleOrder $registeredOrder */
        $registeredOrder = $this->daoEntitySaleOrder->getById($orderIdMage);
        $isRegistered = (bool)$registeredOrder;
        /* skip processing for registered orders or guest checkouted */
        if ($orderIdMage && !$isRegistered && $customerIdMage) {
            $odooOrder = $this->subCollector->getSaleOrder($mageOrder);
            /* save order into Odoo repo */
            $resp = $this->daoOdooSaleOrder->save($odooOrder);
            $result->setOdooResponse($resp);
            if ($resp instanceof \Praxigento\Odoo\Data\Odoo\SaleOrder\Response) {
                $mageId = $mageOrder->getEntityId();
                $odooId = $resp->getIdOdoo();
                /* mark order as replicated */
                $registry = new \Praxigento\Odoo\Repo\Data\SaleOrder();
                $registry->setMageRef($mageId);
                $registry->setOdooRef($odooId);
                $this->daoEntitySaleOrder->create($registry);
                /* finalize transaction */
                $result->markSucceed();
            }
        } else {
            $msg = "Order replication to Odoo is skipped (id/is_registered/customer_id): $orderIdMage/"
                . (string)$isRegistered . "/$customerIdMage.";
            $this->logger->info($msg);
        }
        return $result;
    }

}