<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;

class Order
{
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\Order\A\Collector */
    private $actCollector;
    /** @var \Praxigento\Odoo\Repo\Dao\SaleOrder */
    private $daoEntitySaleOrder;
    /** @var \Praxigento\Odoo\Repo\Odoo\SaleOrder */
    private $daoOdooSaleOrder;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;

    public function __construct(
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Odoo\Repo\Dao\SaleOrder $daoEntitySaleOrder,
        \Praxigento\Odoo\Repo\Odoo\SaleOrder $daoOdooSaleOrder,
        \Praxigento\Odoo\Service\Replicate\Sale\Order\A\Collector $collector
    ) {
        $this->logger = $logger;
        $this->daoEntitySaleOrder = $daoEntitySaleOrder;
        $this->daoOdooSaleOrder = $daoOdooSaleOrder;
        $this->actCollector = $collector;
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
        /* skip processing for registered orders or orders being checked out by guests */
        if ($orderIdMage && !$isRegistered && $customerIdMage) {
            $odooOrder = $this->actCollector->getSaleOrder($mageOrder);
            /* save order into Odoo repo */
            $resp = $this->daoOdooSaleOrder->save($odooOrder);
            $result->setOdooResponse($resp);
            if ($resp instanceof \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Response) {
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