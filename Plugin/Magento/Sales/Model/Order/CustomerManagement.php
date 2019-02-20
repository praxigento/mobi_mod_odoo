<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Plugin\Magento\Sales\Model\Order;


class CustomerManagement
{
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    private $daoOrder;
    /** @var  \Praxigento\Odoo\Service\Replicate\Sale\Order */
    private $servReplicate;

    public function __construct(
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $daoOrder,
        \Praxigento\Odoo\Service\Replicate\Sale\Order $servReplicate
    ) {
        $this->logger = $logger;
        $this->daoOrder = $daoOrder;
        $this->servReplicate = $servReplicate;
    }

    public function aroundCreate(
        \Magento\Sales\Model\Order\CustomerManagement $subject,
        \Closure $proceed,
        $orderId
    ) {
        $result = $proceed($orderId);
        /* SAN-535: disable async orders replication with Odoo */
//        try {
//            $req = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Request();
//            $order = $this->daoOrder->get($orderId);
//            $req->setSaleOrder($order);
//            $this->servReplicate->exec($req);
//        } catch (\Throwable $th) {
//
//        }
        return $result;
    }
}