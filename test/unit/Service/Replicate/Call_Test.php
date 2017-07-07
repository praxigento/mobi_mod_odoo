<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoEntitySaleOrder;
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
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $logger = $this->_mockLogger();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoEntitySaleOrder = $this->_mock(\Praxigento\Odoo\Repo\Entity\ISaleOrder::class);
        $this->mRepoOdooInventory = $this->_mock(\Praxigento\Odoo\Repo\Odoo\IInventory::class);
        $this->mRepoOdooSaleOrder = $this->_mock(\Praxigento\Odoo\Repo\Odoo\ISaleOrder::class);
        $this->mSubCollector = $this->_mock(Sub\OdooDataCollector::class);
        $this->mSubReplicator = $this->_mock(Sub\Replicator::class);
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $logger,
            $this->mManTrans,
            $this->mRepoEntitySaleOrder,
            $this->mRepoOdooInventory,
            $this->mRepoOdooSaleOrder,
            $this->mSubCollector,
            $this->mSubReplicator
        ];
        /** create object to test */
        $this->obj = new Call(
            $logger,
            $this->mManTrans,
            $this->mRepoEntitySaleOrder,
            $this->mRepoOdooInventory,
            $this->mRepoOdooSaleOrder,
            $this->mSubCollector,
            $this->mSubReplicator
        );
    }

    public function test__doProductReplication()
    {
        /** === Test Data === */
        $INVENTORY = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory::class);
        /** === Setup Mocks === */
        // $warehouses = $inventory->getWarehouses();
        $mWarehouses = 'warehouses';
        $INVENTORY
            ->shouldReceive('getWarehouses')->once()
            ->andReturn($mWarehouses);
        // $lots = $inventory->getLots();
        $mLots = 'lots';
        $INVENTORY
            ->shouldReceive('getLots')->once()
            ->andReturn($mLots);
        // $products = $inventory->getProducts();
        $mProducts = ['products'];
        $INVENTORY
            ->shouldReceive('getProducts')->once()
            ->andReturn($mProducts);
        // $this->_subReplicator->processWarehouses($warehouses);
        $this->mSubReplicator
            ->shouldReceive('processWarehouses')->once()
            ->with($mWarehouses);
        // $this->_subReplicator->processLots($lots);
        $this->mSubReplicator
            ->shouldReceive('processLots')->once()
            ->with($mLots);
        // $this->_subReplicator->processProductItem($prod);
        $this->mSubReplicator
            ->shouldReceive('processProductItem')->once();
        /** === Call and asserts  === */
        $this->obj->_doProductReplication($INVENTORY);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Call::class, $this->obj);
    }

    public function test_productSave()
    {
        /** === Test Data === */
        $BUNDLE = new \Praxigento\Odoo\Data\Odoo\Inventory();
        $mProd = new \Praxigento\Odoo\Data\Odoo\Inventory\Product();
        $BUNDLE->setProducts([$mProd]);
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(Call::class . '[_doProductReplication]', $this->objArgs);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $this->_doProductReplication($bundle);
        $this->obj
            ->shouldReceive('_doProductReplication')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\ProductSave();
        $req->setProductBundle($BUNDLE);
        $res = $this->obj->productSave($req);
        $this->assertTrue($res->isSucceed());
    }

    /**
     * @expectedException \Exception
     */
    public function test_productsFromOdoo_fail()
    {
        /** === Test Data === */
        $PROD_ID_ODOO = 21;
        $BUNDLE = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory::class);
        $BUNDLE->shouldReceive('getOption', 'getWarehouses', 'getLots');
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(Call::class . '[_doProductReplication]', $this->objArgs);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $bundle = $this->_repoOdooInventory->get($ids);
        $this->mRepoOdooInventory
            ->shouldReceive('get')->once()
            ->andThrow(new \Exception());
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\ProductsFromOdoo();
        $req->setOdooIds($PROD_ID_ODOO);
        $this->obj->productsFromOdoo($req);
    }

    public function test_productsFromOdoo_success()
    {
        /** === Test Data === */
        $PROD_ID_ODOO = 21;
        $BUNDLE = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory::class);
        $BUNDLE->shouldReceive('getOption', 'getWarehouses', 'getLots');
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(Call::class . '[_doProductReplication]', $this->objArgs);
        /** === Setup Mocks === */
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $bundle = $this->_repoOdooInventory->get($ids);
        $this->mRepoOdooInventory
            ->shouldReceive('get')->once()
            ->andReturn($BUNDLE);
        // $this->_doProductReplication($bundle);
        $this->obj
            ->shouldReceive('_doProductReplication')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
        /** === Call and asserts  === */
        $req = new Request\ProductsFromOdoo();
        $req->setOdooIds($PROD_ID_ODOO);
        $res = $this->obj->productsFromOdoo($req);
        $this->assertTrue($res->isSucceed());
    }

}