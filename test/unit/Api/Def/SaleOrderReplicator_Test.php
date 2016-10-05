<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Def;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaleOrderReplicator_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mCallReplicate;
    /** @var  \Mockery\MockInterface */
    private $mCollector;
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
        $this->mCallReplicate = $this->_mock(\Praxigento\Odoo\Service\IReplicate::class);
        $this->mCollector = $this->_mock(\Praxigento\Odoo\Api\Def\SaleOrderReplicator\Collector::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new SaleOrderReplicator(
            $this->mLogger,
            $this->mManObj,
            $this->mManInvoice,
            $this->mManTrans,
            $this->mManBusCodes,
            $this->mShipmentLoader,
            $this->mCallReplicate,
            $this->mCollector
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(\Praxigento\Odoo\Api\SaleOrderReplicatorInterface::class, $this->obj);
    }

    public function test_orderPushRepeat_error()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(\Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report::class);
        $mResult = new \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(\Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report::class)
            ->andReturn($mResult);
        // $orders = $this->_collector->getOrdersToReplicate();
        $mOrder = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $mOrders = [$mOrder];
        $this->mCollector
            ->shouldReceive('getOrdersToReplicate')->once()
            ->andReturn($mOrders);
        // $req = $this->_manObj->create(\Praxigento\Odoo\Service\Replicate\Request\OrderSave::class);
        $mReq = new \Praxigento\Odoo\Service\Replicate\Request\OrderSave();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(\Praxigento\Odoo\Service\Replicate\Request\OrderSave::class)
            ->andReturn($mReq);
        // $resp = $this->_callReplicate->orderSave($req);
        $mResp = new \Praxigento\Odoo\Service\Replicate\Response\OrderSave();
        $this->mCallReplicate
            ->shouldReceive('orderSave')->once()
            ->with($mReq)
            ->andReturn($mResp);
        // $respOdoo = $resp->getOdooResponse();
        $mRespOdoo = new \Praxigento\Odoo\Data\Odoo\Error();
        $mResp->setOdooResponse($mRespOdoo);
        // $reportEntry = $this->_manObj->create(\Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report\Entry::class);
        $mReportEntry = new \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report\Entry();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(\Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report\Entry::class)
            ->andReturn($mReportEntry);
        // $id = $order->getEntityId();
        $mId = 32;
        $mOrder->shouldReceive('getEntityId')->once()
            ->andReturn($mId);
        // $number = $order->getIncrementId();
        $mNumber = 64;
        $mOrder->shouldReceive('getIncrementId')->once()
            ->andReturn($mNumber);
        //
        /** === Call and asserts  === */
        $res = $this->obj->orderPushRepeat();
        $entries = $res->getEntries();
        $entry = current($entries);
        $this->assertFalse($entry->getIsSucceed());
    }

    public function test_orderPushRepeat_success()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(\Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report::class);
        $mResult = new \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(\Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report::class)
            ->andReturn($mResult);
        // $orders = $this->_collector->getOrdersToReplicate();
        $mOrder = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $mOrders = [$mOrder];
        $this->mCollector
            ->shouldReceive('getOrdersToReplicate')->once()
            ->andReturn($mOrders);
        // $req = $this->_manObj->create(\Praxigento\Odoo\Service\Replicate\Request\OrderSave::class);
        $mReq = new \Praxigento\Odoo\Service\Replicate\Request\OrderSave();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(\Praxigento\Odoo\Service\Replicate\Request\OrderSave::class)
            ->andReturn($mReq);
        // $resp = $this->_callReplicate->orderSave($req);
        $mResp = new \Praxigento\Odoo\Service\Replicate\Response\OrderSave();
        $this->mCallReplicate
            ->shouldReceive('orderSave')->once()
            ->with($mReq)
            ->andReturn($mResp);
        // $respOdoo = $resp->getOdooResponse();
        $mRespOdoo = 'odoo data';
        $mResp->setOdooResponse($mRespOdoo);
        // $reportEntry = $this->_manObj->create(\Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report\Entry::class);
        $mReportEntry = new \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report\Entry();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->with(\Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report\Entry::class)
            ->andReturn($mReportEntry);
        // $id = $order->getEntityId();
        $mId = 32;
        $mOrder->shouldReceive('getEntityId')->once()
            ->andReturn($mId);
        // $number = $order->getIncrementId();
        $mNumber = 64;
        $mOrder->shouldReceive('getIncrementId')->once()
            ->andReturn($mNumber);
        //
        /** === Call and asserts  === */
        $res = $this->obj->orderPushRepeat();
        $entries = $res->getEntries();
        $entry = current($entries);
        $this->assertTrue($entry->getIsSucceed());
    }

    /**
     * @expectedException \Exception
     */
    public function test_shipmentTrackingSave_exception()
    {
        /** === Test Data === */
        $orderIdMage = 16;
        $shipmentIdOdoo = 32;
        $shippingCode = 'shipping code';
        $trackingNum = 'tracking number';
        $trackInfo = new \Praxigento\Odoo\Data\Odoo\Shipment\TrackingInfo();
        $trackInfo->setShippingCode($shippingCode);
        $trackInfo->setTrackingNumber($trackingNum);
        $shipment = new \Praxigento\Odoo\Data\Odoo\Shipment();
        $shipment->setIdOdoo($shipmentIdOdoo);
        $shipment->setTrackingInfo($trackInfo);
        $data = new \Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking ();
        $data->setSaleOrderIdMage($orderIdMage);
        $data->setShipment($shipment);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mock(\Praxigento\Core\Transaction\Database\IDefinition::class);
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $this->_shipmentLoader->setOrderId($orderIdMage);
        $this->mShipmentLoader
            ->shouldReceive('setOrderId')->once()
            ->with($orderIdMage)
            ->andThrow(new \Exception());
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $this->obj->shipmentTrackingSave($data);
    }

    public function test_shipmentTrackingSave_success()
    {
        /** === Test Data === */
        $orderIdMage = 16;
        $shipmentIdOdoo = 32;
        $shippingCode = 'shipping code';
        $trackingNum = 'tracking number';
        $carrierCode = 'carrier code';
        $title = 'carrier title';
        $trackInfo = new \Praxigento\Odoo\Data\Odoo\Shipment\TrackingInfo();
        $trackInfo->setShippingCode($shippingCode);
        $trackInfo->setTrackingNumber($trackingNum);
        $shipment = new \Praxigento\Odoo\Data\Odoo\Shipment();
        $shipment->setIdOdoo($shipmentIdOdoo);
        $shipment->setTrackingInfo($trackInfo);
        $data = new \Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking ();
        $data->setSaleOrderIdMage($orderIdMage);
        $data->setShipment($shipment);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mock(\Praxigento\Core\Transaction\Database\IDefinition::class);
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $this->_shipmentLoader->setOrderId($orderIdMage);
        $this->mShipmentLoader
            ->shouldReceive('setOrderId')->once()
            ->with($orderIdMage);
        // $shipment = $this->_shipmentLoader->load();
        $mShipment = $this->_mock(\Magento\Sales\Api\Data\ShipmentInterface::class);
        $this->mShipmentLoader
            ->shouldReceive('load')->once()
            ->andReturn($mShipment);
        // $carrierCode = $this->_manBusCodes->getMagCodeForCarrier($shippingMethodCode);
        $this->mManBusCodes
            ->shouldReceive('getMagCodeForCarrier')->once()
            ->andReturn($carrierCode);
        // $title = $this->_manBusCodes->getTitleForCarrier($shippingMethodCode);
        $this->mManBusCodes
            ->shouldReceive('getTitleForCarrier')->once()
            ->andReturn($title);
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
        $res = $this->obj->shipmentTrackingSave($data);
        $this->assertTrue($res);
    }
}