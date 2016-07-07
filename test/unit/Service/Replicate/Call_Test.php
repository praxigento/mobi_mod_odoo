<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoOdooInventory;
    /** @var  \Mockery\MockInterface */
    private $mRepoOdooSaleOrder;
    /** @var  \Mockery\MockInterface */
    private $mSubCollector;
    /** @var  \Mockery\MockInterface */
    private $mSubReplicator;
    /** @var  Call */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoOdooInventory = $this->_mock(\Praxigento\Odoo\Repo\Odoo\IInventory::class);
        $this->mRepoOdooSaleOrder = $this->_mock(\Praxigento\Odoo\Repo\Odoo\ISaleOrder::class);
        $this->mSubCollector = $this->_mock(Sub\OdooDataCollector::class);
        $this->mSubReplicator = $this->_mock(Sub\Replicator::class);
        /** create object to test */
        $this->obj = new Call(
            $this->mManTrans,
            $this->mRepoOdooInventory,
            $this->mRepoOdooSaleOrder,
            $this->mSubCollector,
            $this->mSubReplicator
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Call::class, $this->obj);
    }

    public function test_productSave()
    {
        /** === Test Data === */
        $PROD_ID = 21;
        $BUNDLE = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory::class);
        $BUNDLE->shouldReceive('getOption', 'getWarehouses', 'getLots');
        /** === Setup Mocks === */
        // $trans = $this->_manTrans->transactionBegin();
        $mTrans = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('transactionBegin')->once()
            ->andReturn($mTrans);
        //
        // $this->_doProductReplication($bundle);
        //
        // $products = $bundle->getProducts();
        $mProd = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory\IProduct::class);
        $BUNDLE->shouldReceive('getProducts')->once()
            ->andReturn([$PROD_ID => $mProd]);
        // $this->_subReplicator->processWarehouses($warehouses);
        $this->mSubReplicator
            ->shouldReceive('processWarehouses')->once();
        // $this->_subReplicator->processLots($lots);
        $this->mSubReplicator
            ->shouldReceive('processLots')->once();
        // $this->_subReplicator->processProductItem($prod);
        $this->mSubReplicator
            ->shouldReceive('processProductItem')->once();
        //
        // $this->_manTrans->transactionCommit($trans);
        $this->mManTrans
            ->shouldReceive('transactionCommit')->once();
        // $this->_manTrans->transactionClose($trans);
        $this->mManTrans
            ->shouldReceive('transactionClose')->once();
        /** === Call and asserts  === */
        $req = new Request\ProductSave();
        $req->setProductBundle($BUNDLE);
        $res = $this->obj->productSave($req);
        $this->assertTrue($res->isSucceed());
    }

    public function test_productsFromOdoo()
    {
        /** === Test Data === */
        $PROD_ID_ODOO = 21;
        $BUNDLE = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory::class);
        $BUNDLE->shouldReceive('getOption', 'getWarehouses', 'getLots');
        /** === Setup Mocks === */
        // $trans = $this->_manTrans->transactionBegin();
        $mTrans = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('transactionBegin')->once()
            ->andReturn($mTrans);
        // $bundle = $this->_repoOdooInventory->get($ids);
        $this->mRepoOdooInventory
            ->shouldReceive('get')->once()
            ->andReturn($BUNDLE);
        //
        // $this->_doProductReplication($bundle);
        //
        // $products = $bundle->getProducts();
        $mProd = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory\IProduct::class);
        $BUNDLE->shouldReceive('getProducts')->once()
            ->andReturn([$PROD_ID_ODOO => $mProd]);
        // $this->_subReplicator->processWarehouses($warehouses);
        $this->mSubReplicator
            ->shouldReceive('processWarehouses')->once();
        // $this->_subReplicator->processLots($lots);
        $this->mSubReplicator
            ->shouldReceive('processLots')->once();
        // $this->_subReplicator->processProductItem($prod);
        $this->mSubReplicator
            ->shouldReceive('processProductItem')->once();
        //
        // $this->_manTrans->transactionCommit($trans);
        $this->mManTrans
            ->shouldReceive('transactionCommit')->once();
        // $this->_manTrans->transactionClose($trans);
        $this->mManTrans
            ->shouldReceive('transactionClose')->once();
        /** === Call and asserts  === */
        $req = new Request\ProductsFromOdoo();
        $req->setOdooIds($PROD_ID_ODOO);
        $res = $this->obj->productsFromOdoo($req);
        $this->assertTrue($res->isSucceed());
    }
}