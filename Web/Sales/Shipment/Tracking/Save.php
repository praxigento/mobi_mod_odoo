<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Sales\Shipment\Tracking;

use Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save\Request as ARequest;
use Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save\Response as AResponse;

class Save
    implements \Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\SaveInterface
{
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Odoo\Api\Helper\BusinessCodes */
    private $manBusCodes;
    /** @var  \Magento\Sales\Model\Service\InvoiceService */
    private $manInvoice;
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $manObj;
    /** @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader */
    private $shipmentLoader;

    public function __construct(
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Sales\Api\InvoiceManagementInterface $manInvoice,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Odoo\Api\Helper\BusinessCodes $manBusCodes,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
    ) {
        $this->logger = $logger;
        $this->manObj = $manObj;
        $this->manInvoice = $manInvoice;
        $this->manTrans = $manTrans;
        $this->manBusCodes = $manBusCodes;
        $this->shipmentLoader = $shipmentLoader;
    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $respRes = new \Praxigento\Core\Api\App\Web\Response\Result();

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
                $carrierCode = $this->manBusCodes->getMageCodeForCarrier($shippingMethodCode);
                $title = $this->manBusCodes->getTitleForCarrier($shippingMethodCode);
                if ($trackNumber) {
                    $track = $this->manObj->create(\Magento\Sales\Model\Order\Shipment\Track::class);
                    $track->setNumber($trackNumber);
                    $track->setCarrierCode($carrierCode);
                    $track->setTitle($title);
                    $shipment->addTrack($track);
                }
                $shipment->register();
                $shipment->save();
                $order = $shipment->getOrder();
                $invoices = $order->getInvoiceCollection();
                if (count($invoices)) {
                    /* there is invoices in order; do nothing */
                } else {
                    $invoice = $this->manInvoice->prepareInvoice($order);
                    $invoice->register();
                    $invoice->save();
                }
                $order->save();
                $this->manTrans->commit($def);
                $respRes->setCode(AResponse::CODE_SUCCESS);
                $this->logger->info("Shipment data (code: $shippingMethodCode; tracking: $trackNumber) is saved for order #$orderIdMage.");
            } else {
                $this->logger->warning("Cannot load shipment for order #$orderIdMage. Nothing to process.");
            }
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->manTrans->end($def);
        }

        /** compose result */
        $result = new AResponse();
        $result->setResult($respRes);
        return $result;
    }
}