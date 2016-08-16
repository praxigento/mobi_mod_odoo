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
    protected $_logger;
    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;
    /** @var  \Praxigento\Odoo\Service\IReplicate */
    protected $_callReplicate;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Odoo\Service\IReplicate $callReplicate
    ) {
        $this->_logger = $logger;
        $this->_manStock = $manStock;
        $this->_callReplicate = $callReplicate;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getData(self::DATA_ORDER);
        $state = $order->getState();
        if ($state == \Magento\Sales\Model\Order::STATE_PROCESSING) {
            try {
                $this->_logger->debug("Call to Odoo service to replicate order.");
                $req = new \Praxigento\Odoo\Service\Replicate\Request\OrderSave();
                $req->setSaleOrder($order);
                $this->_callReplicate->orderSave($req);
            } catch (\Exception $e) {
                /* catch all exceptions and steal them */
                $msg = 'Some error is occurred on sale order saving to Odoo. Error: ' . $e->getMessage();
                $this->_logger->error($msg);
            }
        }
    }

}