<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Sales\Shipment\Tracking;

class Save
    implements \Praxigento\Odoo\Api\Sales\Shipment\Tracking\SaveInterface
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var \Praxigento\Odoo\Tool\IBusinessCodesManager */
    protected $manBusCodes;
    /** @var  \Magento\Sales\Model\Service\InvoiceService */
    protected $manInvoice;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var  \Praxigento\Core\App\Api\Repo\Transaction\Manager */
    protected $manTrans;
    /** @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader */
    protected $shipmentLoader;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Sales\Api\InvoiceManagementInterface $manInvoice,
        \Praxigento\Core\App\Api\Repo\Transaction\Manager $manTrans,
        \Praxigento\Odoo\Tool\IBusinessCodesManager $manBusCodes,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
    ) {
        $this->logger = $logger;
        $this->manObj = $manObj;
        $this->manInvoice = $manInvoice;
        $this->manTrans = $manTrans;
        $this->manBusCodes = $manBusCodes;
        $this->shipmentLoader = $shipmentLoader;
    }

    public function execute(\Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking $data)
    {
        $result = false;
        /* replicate all data in one transaction */
        $def = $this->manTrans->begin();
        try {
            $orderIdMage = $data->getSaleOrderIdMage();
            $trackNumber = $data->get('shipment/trackingInfo/trackingNumber');
            $shippingMethodCode = $data->get('shipment/trackingInfo/shippingCode');
            $this->shipmentLoader->setOrderId($orderIdMage);
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $shipment = $this->shipmentLoader->load();
            if ($shipment) {
                $carrierCode = $this->manBusCodes->getMagCodeForCarrier($shippingMethodCode);
                $title = $this->manBusCodes->getTitleForCarrier($shippingMethodCode);
                $track = $this->manObj->create(\Magento\Sales\Model\Order\Shipment\Track::class);
                $track->setNumber($trackNumber);
                $track->setCarrierCode($carrierCode);
                $track->setTitle($title);
                $shipment->addTrack($track);
                $shipment->register();
                $shipment->save();
                $order = $shipment->getOrder();
                $invoice = $this->manInvoice->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $order->save();
                $this->manTrans->commit($def);
                $result = true;
                $this->logger->info("Shipment data (code: $shippingMethodCode; tracking: $trackNumber) is saved for order #$orderIdMage.");
            } else {
                $this->logger->warning("Cannot load shipment for order #$orderIdMage. Nothing to process.");
            }
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->manTrans->end($def);
        }
        return $result;
    }
}