<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate;

use Praxigento\Core\Repo\ITransactionManager;
use Praxigento\Odoo\Data\Api\IBundle;
use Praxigento\Odoo\Repo\Odoo\IInventory as RepoOdooIInventory;
use Praxigento\Odoo\Repo\Odoo\ISaleOrder as RepoOdooISaleOrder;
use Praxigento\Odoo\Service\IReplicate;
use Praxigento\Odoo\Service\Replicate;

class Call implements IReplicate
{
    /** @var  ITransactionManager */
    protected $_manTrans;
    /** @var RepoOdooIInventory */
    protected $_repoOdooInventory;
    /** @var RepoOdooISaleOrder */
    protected $_repoOdooSaleOrder;
    /** @var  Sub\Replicator */
    protected $_subReplicator;


    public function __construct(
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Praxigento\Odoo\Repo\Odoo\IInventory $repoOdooInventory,
        \Praxigento\Odoo\Repo\Odoo\ISaleOrder $repoOdooSaleOrder,
        Sub\Replicator $subReplicator
    ) {
        $this->_manTrans = $manTrans;
        $this->_repoOdooInventory = $repoOdooInventory;
        $this->_repoOdooSaleOrder = $repoOdooSaleOrder;
        $this->_subReplicator = $subReplicator;
    }

    /**
     * Perform products bundle replication.
     *
     * @param IBundle $bundle
     * @throws \Exception
     */
    protected function _doProductReplication(IBundle $bundle)
    {
        $options = $bundle->getOption();
        $warehouses = $bundle->getWarehouses();
        $lots = $bundle->getLots();
        $products = $bundle->getProducts();
        /* replicate warehouses & lots */
        $this->_subReplicator->processWarehouses($warehouses);
        $this->_subReplicator->processLots($lots);
        /* replicate products */
        foreach ($products as $odooId => $prod) {
            $this->_subReplicator->processProductItem($prod);
        }
    }

    /** @inheritdoc */
    public function orderSave(Replicate\Request\OrderSave $req)
    {
        $result = new Response\OrderSave();
        $order = $req->getSaleOrder();
        $resp = $this->_repoOdooSaleOrder->save($order);
        $result->setOdooResponse($resp);
        return $result;
    }

    /** @inheritdoc */
    public function productSave(Request\ProductSave $req)
    {
        $result = new Response\ProductSave();
        /** @var  $bundle IBundle */
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
    public function productsFromOdoo(Request\ProductsFromOdoo $req)
    {
        $result = new Response\ProductsFromOdoo();
        /* replicate all data in one transaction */
        $trans = $this->_manTrans->transactionBegin();
        try {
            $ids = $req->getOdooIds();
            /** @var  $bundle IBundle */
            $bundle = $this->_repoOdooInventory->get($ids);
            $this->_doProductReplication($bundle);
            $this->_manTrans->transactionCommit($trans);
            $result->markSucceed();
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }
}