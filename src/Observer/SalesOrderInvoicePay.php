<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Observer;

/**
 * Replicate paid order to Odoo on invoice payments (check/money order).
 */
class SalesOrderInvoicePay
    implements \Magento\Framework\Event\ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INVOICE = 'invoice';
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var  \Praxigento\Warehouse\Tool\IStockManager */
    protected $manStock;
    /** @var  \Praxigento\Odoo\Service\Replicate\Sale\IOrder */
    protected $callReplicate;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Odoo\Service\Replicate\Sale\IOrder $callReplicate
    ) {
        $this->logger = $logger;
        $this->manStock = $manStock;
        $this->callReplicate = $callReplicate;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData(self::DATA_INVOICE);
        $state = $invoice->getState();
        if ($state == \Magento\Sales\Model\Order\Invoice::STATE_PAID) {
            try {
                $this->logger->debug("Call to Odoo service to replicate order.");
                $req = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Request();
                $order = $invoice->getOrder();
                $req->setSaleOrder($order);
                $this->callReplicate->exec($req);
            } catch (\Exception $e) {
                /* catch all exceptions and steal them */
                $msg = 'Some error is occurred on sale order saving to Odoo. Error: ' . $e->getMessage();
                $this->logger->error($msg);
            }
        }
    }

}