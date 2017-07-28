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
    /** @var \Praxigento\Odoo\Repo\Entity\Def\SaleOrder */
    protected $_repoEntitySaleOrder;
    /** @var \Praxigento\Odoo\Repo\Odoo\IInventory */
    protected $_repoOdooInventory;
    /** @var \Praxigento\Odoo\Repo\Odoo\ISaleOrder */
    protected $_repoOdooSaleOrder;
    /** @var  Sub\Replicator */
    protected $_subReplicator;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Praxigento\Odoo\Repo\Entity\Def\SaleOrder $repoEntitySaleOrder,
        \Praxigento\Odoo\Repo\Odoo\IInventory $repoOdooInventory,
        \Praxigento\Odoo\Repo\Odoo\ISaleOrder $repoOdooSaleOrder,
        Sub\Replicator $subReplicator
    ) {
        $this->_logger = $logger;
        $this->_manTrans = $manTrans;
        $this->_repoEntitySaleOrder = $repoEntitySaleOrder;
        $this->_repoOdooInventory = $repoOdooInventory;
        $this->_repoOdooSaleOrder = $repoOdooSaleOrder;
        $this->_subReplicator = $subReplicator;
    }

    /**
     * Perform products bundle replication.
     *
     * @param \Praxigento\Odoo\Data\Odoo\Inventory $inventory
     * @throws \Exception
     */
    public function _doProductReplication(
        \Praxigento\Odoo\Data\Odoo\Inventory $inventory
    ) {
        // $options = $inventory->getOption();
        $warehouses = $inventory->getWarehouses();
        $lots = $inventory->getLots();
        $products = $inventory->getProducts();
        /* replicate warehouses & lots */
        $this->_subReplicator->processWarehouses($warehouses);
        $this->_subReplicator->processLots($lots);
        /* replicate products */
        foreach ($products as $prod) {
            $this->_subReplicator->processProductItem($prod);
        }
    }

    public function orderSave(
        Replicate\Request\OrderSave $req
    ) {
        throw new \Exception("Deprecated. Use \Praxigento\Odoo\Service\Replicate\Sale\IOrder.");
    }

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
}