<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Plugin\Sales\Model\Order;


class CustomerManagement
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    private $repoOrder;
    /** @var  \Praxigento\Odoo\Service\Replicate\Sale\IOrder */
    private $servReplicate;

    public function __construct(
        \Praxigento\Core\App\Logger\App $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $repoOrder,
        \Praxigento\Odoo\Service\Replicate\Sale\IOrder $servReplicate
    ) {
        $this->logger = $logger;
        $this->repoOrder = $repoOrder;
        $this->servReplicate = $servReplicate;
    }

    public function aroundCreate(
        \Magento\Sales\Model\Order\CustomerManagement $subject,
        \Closure $proceed,
        $orderId
    ) {
        $result = $proceed($orderId);
        try {
            $req = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Request();
            $order = $this->repoOrder->get($orderId);
            $req->setSaleOrder($order);
            $this->servReplicate->exec($req);
        } catch (\Throwable $th) {

        }
        return $result;
    }
}