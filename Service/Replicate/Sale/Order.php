<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;

class Order
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Odoo\Service\Replicate\Sale\IOrder
{
    /** @var \Praxigento\Odoo\Repo\Entity\SaleOrder */
    protected $repoEntitySaleOrder;
    /** @var \Praxigento\Odoo\Repo\Odoo\ISaleOrder */
    protected $repoOdooSaleOrder;
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\Order\Collector */
    protected $subCollector;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Repo\Entity\SaleOrder $repoEntitySaleOrder,
        \Praxigento\Odoo\Repo\Odoo\ISaleOrder $repoOdooSaleOrder,
        \Praxigento\Odoo\Service\Replicate\Sale\Order\Collector $collector
    ) {
        parent::__construct($logger, $manObj);
        $this->repoEntitySaleOrder = $repoEntitySaleOrder;
        $this->repoOdooSaleOrder = $repoOdooSaleOrder;
        $this->subCollector = $collector;
    }

    public function exec(\Praxigento\Odoo\Service\Replicate\Sale\Order\Request $req)
    {
        $result = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Response();
        /** @var \Magento\Sales\Api\Data\OrderInterface $mageOrder */
        $mageOrder = $req->getSaleOrder();
        $orderIdMage = $mageOrder->getEntityId();
        $customerIdMage = $mageOrder->getCustomerId();
        /** @var \Praxigento\Odoo\Repo\Entity\Data\SaleOrder $registeredOrder */
        $registeredOrder = $this->repoEntitySaleOrder->getById($orderIdMage);
        $isRegistered = (bool)$registeredOrder;
        /* skip processing for registered orders or guest checkouted */
        if ($orderIdMage && !$isRegistered && $customerIdMage) {
            $odooOrder = $this->subCollector->getSaleOrder($mageOrder);
            /* save order into Odoo repo */
            $resp = $this->repoOdooSaleOrder->save($odooOrder);
            $result->setOdooResponse($resp);
            if ($resp instanceof \Praxigento\Odoo\Data\Odoo\SaleOrder\Response) {
                $mageId = $mageOrder->getEntityId();
                $odooId = $resp->getIdOdoo();
                /* mark order as replicated */
                $registry = new \Praxigento\Odoo\Repo\Entity\Data\SaleOrder();
                $registry->setMageRef($mageId);
                $registry->setOdooRef($odooId);
                $this->repoEntitySaleOrder->create($registry);
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