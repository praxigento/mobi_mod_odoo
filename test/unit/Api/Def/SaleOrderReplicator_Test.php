<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Def;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class SaleOrderReplicator_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mManBusCodes;
    /** @var  \Mockery\MockInterface */
    private $mManInvoice;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mShipmentLoader;
    /** @var  SaleOrderReplicator */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mManObj = $this->_mockObjectManager();
        $this->mManInvoice = $this->_mock(\Magento\Sales\Api\InvoiceManagementInterface::class);
        $this->mManTrans = $this->_mock(\Praxigento\Core\Transaction\Database\IManager::class);
        $this->mManBusCodes = $this->_mock(\Praxigento\Odoo\Tool\IBusinessCodesManager::class);
        $this->mShipmentLoader = $this->_mock(\Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new SaleOrderReplicator(
            $this->mLogger,
            $this->mManObj,
            $this->mManInvoice,
            $this->mManTrans,
            $this->mManBusCodes,
            $this->mShipmentLoader
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(\Praxigento\Odoo\Api\SaleOrderReplicatorInterface::class, $this->obj);
    }

    /**
     * @expectedException \Exception
     */
    public function test_shipmentTrackingSave_exception()
    {
        /** === Test Data === */
        $ORDER_ID_MAGE = 16;
        $SHIPMENT_ID_ODOO = 32;
        $SHIPPING_CODE = 'shipping code';
        $TRACKING_NUM = 'tracking number';
        $TRACK_INFO = new \Praxigento\Odoo\Data\Odoo\Shipment\TrackingInfo();
        $TRACK_INFO->setShippingCode($SHIPPING_CODE);
        $TRACK_INFO->setTrackingNumber($TRACKING_NUM);
        $SHIPMENT = new \Praxigento\Odoo\Data\Odoo\Shipment();
        $SHIPMENT->setIdOdoo($SHIPMENT_ID_ODOO);
        $SHIPMENT->setTrackingInfo($TRACK_INFO);
        $DATA = new \Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking ();
        $DATA->setSaleOrderIdMage($ORDER_ID_MAGE);
        $DATA->setShipment($SHIPMENT);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mock(\Praxigento\Core\Transaction\Database\IDefinition::class);
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $this->_shipmentLoader->setOrderId($orderIdMage);
        $this->mShipmentLoader
            ->shouldReceive('setOrderId')->once()
            ->with($ORDER_ID_MAGE)
            ->andThrow(new \Exception());
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $this->obj->shipmentTrackingSave($DATA);
    }

    public function test_shipmentTrackingSave_success()
    {
        /** === Test Data === */
        $ORDER_ID_MAGE = 16;
        $SHIPMENT_ID_ODOO = 32;
        $SHIPPING_CODE = 'shipping code';
        $TRACKING_NUM = 'tracking number';
        $CARRIER_CODE = 'carrier code';
        $TITLE = 'carrier title';
        $TRACK_INFO = new \Praxigento\Odoo\Data\Odoo\Shipment\TrackingInfo();
        $TRACK_INFO->setShippingCode($SHIPPING_CODE);
        $TRACK_INFO->setTrackingNumber($TRACKING_NUM);
        $SHIPMENT = new \Praxigento\Odoo\Data\Odoo\Shipment();
        $SHIPMENT->setIdOdoo($SHIPMENT_ID_ODOO);
        $SHIPMENT->setTrackingInfo($TRACK_INFO);
        $DATA = new \Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking ();
        $DATA->setSaleOrderIdMage($ORDER_ID_MAGE);
        $DATA->setShipment($SHIPMENT);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mock(\Praxigento\Core\Transaction\Database\IDefinition::class);
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $this->_shipmentLoader->setOrderId($orderIdMage);
        $this->mShipmentLoader
            ->shouldReceive('setOrderId')->once()
            ->with($ORDER_ID_MAGE);
        // $shipment = $this->_shipmentLoader->load();
        $mShipment = $this->_mock(\Magento\Sales\Api\Data\ShipmentInterface::class);
        $this->mShipmentLoader
            ->shouldReceive('load')->once()
            ->andReturn($mShipment);
        // $carrierCode = $this->_manBusCodes->getMagCodeForCarrier($shippingMethodCode);
        $this->mManBusCodes
            ->shouldReceive('getMagCodeForCarrier')->once()
            ->andReturn($CARRIER_CODE);
        // $title = $this->_manBusCodes->getTitleForCarrier($shippingMethodCode);
        $this->mManBusCodes
            ->shouldReceive('getTitleForCarrier')->once()
            ->andReturn($TITLE);
        // $track = $this->_manObj->create(\Magento\Sales\Model\Order\Shipment\Track::class);
        $mTrack = $this->_mock(\Magento\Sales\Model\Order\Shipment\Track::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mTrack);
        // $track->setNumber($trackNumber);
        $mTrack->shouldReceive('setNumber')->once();
        //$track->setCarrierCode($carrierCode);
        $mTrack->shouldReceive('setCarrierCode')->once();
        //$track->setTitle($title);
        $mTrack->shouldReceive('setTitle')->once();
        // $shipment->addTrack($track);
        $mShipment->shouldReceive('addTrack')->once();
        //$shipment->register();
        $mShipment->shouldReceive('register')->once();
        //$shipment->save();
        $mShipment->shouldReceive('save')->once();
        // $order = $shipment->getOrder();
        $mOrder = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $mShipment
            ->shouldReceive('getOrder')->once()
            ->andReturn($mOrder);
        // $invoice = $this->_manInvoice->prepareInvoice($order);
        $mInvoice = $this->_mock(\Magento\Sales\Api\Data\InvoiceInterface::class);
        $this->mManInvoice
            ->shouldReceive('prepareInvoice')->once()
            ->andReturn($mInvoice);
        // $invoice->register();
        $mInvoice->shouldReceive('register')->once();
        // $invoice->save();
        $mInvoice->shouldReceive('save')->once();
        // $order->save();
        $mOrder->shouldReceive('save')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $res = $this->obj->shipmentTrackingSave($DATA);
    }
}