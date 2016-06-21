<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Praxigento\Odoo\Service\Replicate\Request\OrderSave as RequestOrderSave;

/**
 * Replicate paid order to Odoo on invoice payments (check/money order).
 */
class SalesOrderInvoicePay implements ObserverInterface
{
    /* Names for the items in the event's data */
    const DATA_INVOICE = 'invoice';
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
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $observer->getData(self::DATA_INVOICE);
        $state = $invoice->getState();
        if ($state == \Magento\Sales\Model\Order\Invoice::STATE_PAID) {
            $this->_logger->debug("Call to Odoo service to replicate order.");
            $req = new RequestOrderSave();
            /** @var ResponseOrderSave $resp */
//            $resp = $this->_callReplicate->orderSave($req);
        }
        return;
    }

}