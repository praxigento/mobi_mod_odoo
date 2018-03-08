<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Observer;

/**
 * Replicate paid order to Odoo for credit cards payments.
 */
class CheckoutSubmitAllAfter
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_ORDER = 'order';
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var  \Praxigento\Warehouse\Api\Helper\Stock */
    private $manStock;
    /** @var  \Praxigento\Odoo\Service\Replicate\Sale\IOrder */
    private $servReplicate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Warehouse\Api\Helper\Stock $manStock,
        \Praxigento\Odoo\Service\Replicate\Sale\IOrder $servReplicate
    ) {
        $this->logger = $logger;
        $this->manStock = $manStock;
        $this->servReplicate = $servReplicate;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData(self::DATA_ORDER);
        $state = $order->getState();
        if ($state == \Magento\Sales\Model\Order::STATE_PROCESSING) {
            try {
                $this->logger->debug("Call to Odoo service to replicate order.");
                $req = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Request();
                $req->setSaleOrder($order);
                $this->servReplicate->exec($req);
            } catch (\Exception $e) {
                /* catch all exceptions and steal them */
                $msg = 'Some error is occurred on sale order saving to Odoo. Error: ' . $e->getMessage();
                $this->logger->error($msg);
            }
        }
    }

}