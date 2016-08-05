<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Def;

/**
 * Implementation of the \Praxigento\Odoo\Api\SaleOrderReplicatorInterface.
 */
class SaleOrderReplicator
    implements \Praxigento\Odoo\Api\SaleOrderReplicatorInterface
{
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var  \Magento\Sales\Model\Service\InvoiceService */
    protected $_manInvoice;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader */
    protected $_shipmentLoader;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Sales\Api\InvoiceManagementInterface $manInvoice,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Transaction\Database\IManager $manTrans
    ) {
        $this->_logger = $logger;
        $this->_shipmentLoader = $shipmentLoader;
        $this->_manInvoice = $manInvoice;
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
    }

    /** @inheritdoc */
    public function shipmentTrackingSave(\Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking $data)
    {
        $result = false;
        /* replicate all data in one transaction */
        $def = $this->_manTrans->begin();
        try {
            $orderIdMage = $data->getSaleOrderIdMage();
            $trackNumber = $data->getData('shipment/trackingInfo/trackingNumber');
            $carrierCode = $data->getData('shipment/trackingInfo/shippingCode');
            $this->_shipmentLoader->setOrderId($orderIdMage);
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $shipment = $this->_shipmentLoader->load();
            if ($shipment) {
                $track = $this->_manObj->create(\Magento\Sales\Model\Order\Shipment\Track::class);
                $track->setNumber($trackNumber);
                $track->setCarrierCode($carrierCode);
                $track->setTitle('Added by script.');
                $shipment->addTrack($track);
                $shipment->register();
                $shipment->save();
                $order = $shipment->getOrder();
                $invoice = $this->_manInvoice->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $order->save();
                $this->_manTrans->commit($def);
                $result = true;
            }
        } catch (\Exception $e) {
            $msg = 'Product replication from Odoo is failed. Error: ' . $e->getMessage();
            $this->_logger->emergency($msg);
            $traceStr = $e->getTraceAsString();
            $this->_logger->emergency($traceStr);
            throw $e;
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
        return $result;
    }
}