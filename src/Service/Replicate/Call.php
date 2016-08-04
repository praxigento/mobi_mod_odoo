<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate;

use Praxigento\Odoo\Data\Odoo\Inventory;
use Praxigento\Odoo\Service\IReplicate;
use Praxigento\Odoo\Service\Replicate;

class Call implements IReplicate
{
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    protected $_repoMageSaleOrder;
    /** @var \Magento\Sales\Api\ShipmentRepositoryInterface */
    protected $_repoMageShipment;
    /** @var \Magento\Sales\Api\ShipmentTrackRepositoryInterface */
    protected $_repoMageShipmentTrack;
    /** @var \Praxigento\Odoo\Repo\Odoo\IInventory */
    protected $_repoOdooInventory;
    /** @var \Praxigento\Odoo\Repo\Odoo\ISaleOrder */
    protected $_repoOdooSaleOrder;
    /** @var  Sub\OdooDataCollector */
    protected $_subCollector;
    /** @var  Sub\Replicator */
    protected $_subReplicator;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader */
    protected $_shipmentLoader;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Sales\Api\OrderRepositoryInterface $repoMageSaleOrder,
        \Magento\Sales\Api\ShipmentRepositoryInterface $repoMageShipment,
        \Magento\Sales\Api\ShipmentTrackRepositoryInterface $repoMageShipmentTrack,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Praxigento\Odoo\Repo\Odoo\IInventory $repoOdooInventory,
        \Praxigento\Odoo\Repo\Odoo\ISaleOrder $repoOdooSaleOrder,
        Sub\OdooDataCollector $subCollector,
        Sub\Replicator $subReplicator
    ) {
        $this->_logger = $logger;
        $this->_manObj = $manObj;
        $this->_repoMageSaleOrder = $repoMageSaleOrder;
        $this->_repoMageShipment = $repoMageShipment;
        $this->_repoMageShipmentTrack = $repoMageShipmentTrack;
        $this->_shipmentLoader = $shipmentLoader;
        $this->_manTrans = $manTrans;
        $this->_repoOdooInventory = $repoOdooInventory;
        $this->_repoOdooSaleOrder = $repoOdooSaleOrder;
        $this->_subCollector = $subCollector;
        $this->_subReplicator = $subReplicator;
    }

    /**
     * Perform products bundle replication.
     *
     * @param \Praxigento\Odoo\Data\Odoo\Inventory $inventory
     * @throws \Exception
     */
    protected function _doProductReplication(
        \Praxigento\Odoo\Data\Odoo\Inventory $inventory
    ) {
        $options = $inventory->getOption();
        $warehouses = $inventory->getWarehouses();
        $lots = $inventory->getLots();
        $products = $inventory->getProducts();
        /* replicate warehouses & lots */
        $this->_subReplicator->processWarehouses($warehouses);
        $this->_subReplicator->processLots($lots);
        /* replicate products */
        foreach ($products as $odooId => $prod) {
            $this->_subReplicator->processProductItem($prod);
        }
    }

    /** @inheritdoc */
    public function orderSave(
        Replicate\Request\OrderSave $req
    ) {
        $result = new Response\OrderSave();
        $mageOrder = $req->getSaleOrder();
        $odooOrder = $this->_subCollector->getSaleOrder($mageOrder);
        $resp = $this->_repoOdooSaleOrder->save($odooOrder);
        $result->setOdooResponse($resp);
        return $result;
    }

    /** @inheritdoc */
    public function productSave(
        Request\ProductSave $req
    ) {
        $result = new Response\ProductSave();
        /** @var  $bundle \Praxigento\Odoo\Data\Odoo\Inventory */
        $bundle = $req->getProductBundle();
        /* replicate all data in one transaction */
        $def = $this->_manTrans->begin();
        try {
            $this->_doProductReplication($bundle);
            $this->_manTrans->commit($def);
            $result->markSucceed();
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
        return $result;
    }

    /** @inheritdoc */
    public function productsFromOdoo(
        Request\ProductsFromOdoo $req
    ) {
        $result = new Response\ProductsFromOdoo();
        /* replicate all data in one transaction */
        $def = $this->_manTrans->begin();
        try {
            $ids = $req->getOdooIds();
            /** @var  $inventory Inventory */
            $inventory = $this->_repoOdooInventory->get($ids);
            $this->_doProductReplication($inventory);
            $this->_manTrans->commit($def);
            $result->markSucceed();
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

    /** @inheritdoc */
    public function shipmentTrackingSave(Replicate\Request\ShipmentTrackingSave $req)
    {
        $result = new Response\ShipmentTrackingSave();
        /* replicate all data in one transaction */
        $def = $this->_manTrans->begin();
        try {
            $orderIdMage = $req->getSaleOrderIdMage();
            $trackNumber = $req->getData('shipment/trackingInfo/trackingNumber');
            $carrierCode = $req->getData('shipment/trackingInfo/shippingCode');
            /* TODO: convert business code to mage code */
            /** @var \Magento\Sales\Api\Data\OrderInterface $order */
            $order = $this->_repoMageSaleOrder->get($orderIdMage);

            /** @var \Magento\Framework\Api\Filter $filter */
            $filter = $this->_manObj->create(\Magento\Framework\Api\Filter::class);
            $filter->setField(\Magento\Sales\Api\Data\ShipmentInterface::ORDER_ID);
            $filter->setConditionType('eq');
            $filter->setValue($orderIdMage);
            /** @var \Magento\Framework\Api\Search\FilterGroup $filtersGroup */
            $filtersGroup = $this->_manObj->create(\Magento\Framework\Api\Search\FilterGroup::class);
            $filtersGroup->setFilters([$filter]);
            /** @var \Magento\Framework\Api\SearchCriteria $critShipment */
            $critShipment = $this->_manObj->create(\Magento\Framework\Api\SearchCriteria::class);
            $critShipment->setFilterGroups([$filtersGroup]);
            /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $shipmentsCollection */
            $shipmentsCollection = $this->_repoMageShipment->getList($critShipment);
            $found = $shipmentsCollection->getSize();
            if ($found) {
                $shipment = $shipmentsCollection->getFirstItem();
            } else {
                /** @var \Magento\Sales\Api\Data\ShipmentInterface $shipment */
                $shipment = $this->_manObj->create(\Magento\Sales\Api\Data\ShipmentInterface::class);
                $shipment->setOrderId($orderIdMage);
                $this->_repoMageShipment->save($shipment);
            }
            $shipmentIdMage = $shipment->getEntityId();
            /* find tracking code */
            /** @var \Magento\Framework\Api\Filter $filterByOrder */
            $filterByOrder = $this->_manObj->create(\Magento\Framework\Api\Filter::class);
            $filterByOrder->setField(\Magento\Sales\Api\Data\ShipmentTrackInterface::ORDER_ID);
            $filterByOrder->setConditionType('eq');
            $filterByOrder->setValue($orderIdMage);
            /** @var \Magento\Framework\Api\Filter $filterByTrack */
            $filterByTrack = $this->_manObj->create(\Magento\Framework\Api\Filter::class);
            $filterByTrack->setField(\Magento\Sales\Api\Data\ShipmentTrackInterface::TRACK_NUMBER);
            $filterByTrack->setConditionType('eq');
            $filterByTrack->setValue($trackNumber);
            /** @var \Magento\Framework\Api\Search\FilterGroup $filtersGroup */
            $filtersGroup = $this->_manObj->create(\Magento\Framework\Api\Search\FilterGroup::class);
            $filtersGroup->setFilters([$filterByTrack]);
            /** @var \Magento\Framework\Api\SearchCriteria $critShipment */
            $critTrack = $this->_manObj->create(\Magento\Framework\Api\SearchCriteria::class);
            $critTrack->setFilterGroups([$filtersGroup]);
            /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Track\Collection $trackingCollection */
            $trackingCollection = $this->_repoMageShipmentTrack->getList($critTrack);
            $found = $trackingCollection->getSize();
            if (!$found) {
                $this->_shipmentLoader->setOrderId($orderIdMage);
                $this->_shipmentLoader->setShipmentId($shipmentIdMage);
                /** @var \Magento\Sales\Model\Order\Shipment $shipment */
                $shipment = $this->_shipmentLoader->load();
                $track = $this->_manObj->create(\Magento\Sales\Model\Order\Shipment\Track::class);
                $track->setNumber($trackNumber);
                $track->setCarrierCode($carrierCode);
                $track->setTitle('Added by script.');
                $shipment->addTrack($track);
                $shipment->save();
            }
            //$this->_repoMageShipment->getList();
            $this->_manTrans->commit($def);
            $result->markSucceed();
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