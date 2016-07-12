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
    /** @var  \Praxigento\Core\Repo\Transaction\IManager */
    protected $_manTrans;
    /** @var \Praxigento\Odoo\Repo\Odoo\IInventory */
    protected $_repoOdooInventory;
    /** @var \Praxigento\Odoo\Repo\Odoo\ISaleOrder */
    protected $_repoOdooSaleOrder;
    /** @var  Sub\Replicator */
    protected $_subReplicator;
    /** @var  Sub\OdooDataCollector */
    protected $_subCollector;
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Praxigento\Core\Repo\Transaction\IManager $manTrans,
        \Praxigento\Odoo\Repo\Odoo\IInventory $repoOdooInventory,
        \Praxigento\Odoo\Repo\Odoo\ISaleOrder $repoOdooSaleOrder,
        Sub\OdooDataCollector $subCollector,
        Sub\Replicator $subReplicator
    ) {
        $this->_logger = $logger;
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
        $trans = $this->_manTrans->transactionBegin();
        try {
            $this->_doProductReplication($bundle);
            $this->_manTrans->transactionCommit($trans);
            $result->markSucceed();
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }

    /** @inheritdoc */
    public function productsFromOdoo(
        Request\ProductsFromOdoo $req
    ) {
        $result = new Response\ProductsFromOdoo();
        /* replicate all data in one transaction */
        $trans = $this->_manTrans->transactionBegin();
        try {
            $ids = $req->getOdooIds();
            /** @var  $inventory Inventory */
            $inventory = $this->_repoOdooInventory->get($ids);
            $this->_doProductReplication($inventory);
            $this->_manTrans->transactionCommit($trans);
            $result->markSucceed();
        } catch (\Exception $e) {
            $msg = 'Product replication from Odoo is failed. Error: ' . $e->getMessage();
            $this->_logger->emergency($msg);
            $traceStr = $e->getTraceAsString();
            $this->_logger->emergency($traceStr);
            throw $e;
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }
}