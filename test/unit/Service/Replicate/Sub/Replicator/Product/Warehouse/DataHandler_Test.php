<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Warehouse;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');

class DataHandler_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mMageRepoStockItem;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mRepoPvMod;
    /** @var  \Mockery\MockInterface */
    private $mRepoPvStockItem;
    /** @var  \Mockery\MockInterface */
    private $mRepoRegistry;
    /** @var  \Mockery\MockInterface */
    private $mRepoWarehouseEntityStockItem;
    /** @var  \Mockery\MockInterface */
    private $mSubLot;
    /** @var  DataHandler */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mMageRepoStockItem = $this->_mock(\Magento\CatalogInventory\Api\StockItemRepositoryInterface::class);
        $this->mRepoPvMod = $this->_mock(\Praxigento\Odoo\Repo\IPv::class);
        $this->mRepoWarehouseEntityStockItem = $this->_mock(\Praxigento\Warehouse\Repo\Entity\Stock\IItem::class);
        $this->mRepoPvStockItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Stock\IItem::class);
        $this->mSubLot = $this->_mock(\Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Lot::class);
        /** create object to test */
        $this->obj = new DataHandler(
            $this->mManObj,
            $this->mMageRepoStockItem,
            $this->mRepoPvMod,
            $this->mRepoWarehouseEntityStockItem,
            $this->mRepoPvStockItem,
            $this->mSubLot
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(DataHandler::class, $this->obj);
    }


    public function test_createWarehouseData()
    {
        /** === Test Data === */
        $PROD_ID = 21;
        $STOCK_ID = 32;
        $PRICE = 43.32;
        $PV = 54.32;
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(StockItemInterface::class);
        $mResult = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $mResult->shouldReceive('setProductId', 'setStockId', 'setIsInStock', 'setManageStock', 'getItemId');
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mResult);
        // $result = $this->_mageRepoStockItem->save($result);
        $this->mMageRepoStockItem
            ->shouldReceive('save')->once()
            ->andReturn($mResult);
        // $this->_repoWarehouseEntityStockItem->create($bind);
        $this->mRepoWarehouseEntityStockItem
            ->shouldReceive('create')->once();
        // $this->_repoPvStockItem->create($bind);
        $this->mRepoPvStockItem
            ->shouldReceive('create')->once();
        /** === Call and asserts  === */
        $this->obj->createWarehouseData($PROD_ID, $STOCK_ID, $PRICE, $PV);
    }

    public function test_processLots()
    {
        /** === Test Data === */
        $LOT1 = new \Praxigento\Odoo\Data\Api\Bundle\Product\Warehouse\Def\Lot();
        $LOTS = [$LOT1];
        $STOCK_ITEM_ID = 21;
        $STOCK_ITEM = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $QTY = 5400;
        /** === Setup Mocks === */
        // $stockItemId = $stockItem->getItemId();
        $STOCK_ITEM->shouldReceive('getItemId')->once()
            ->andReturn($STOCK_ITEM_ID);
        // $qtyTotal += $this->_subLot->processLot($stockItemId, $lot);
        $this->mSubLot
            ->shouldReceive('processLot')->once()
            ->andReturn($QTY);
        // $stockItem->setQty($qtyTotal);
        $STOCK_ITEM->shouldReceive('setQty');
        // $this->_mageRepoStockItem->save($stockItem);
        $this->mMageRepoStockItem
            ->shouldReceive('save')->once();
        // $this->_subLot->cleanupLots($stockItemId, $lots);
        $this->mSubLot
            ->shouldReceive('cleanupLots')->once();
        /** === Call and asserts  === */
        $this->obj->processLots($LOTS, $STOCK_ITEM);
    }

    public function test_updateWarehouseData_exists_registered()
    {
        /** === Test Data === */
        $STOCK_ITEM_ID = 21;
        $PRICE = 43.32;
        $PV = 54.32;
        /** === Setup Mocks === */
        // $exist = $this->_repoWarehouseEntityStockItem->getById($stockItemRef);
        $this->mRepoWarehouseEntityStockItem
            ->shouldReceive('getById')->once()
            ->andReturn('some data');
        // $this->_repoWarehouseEntityStockItem->updateById($bind, $stockItemId);
        $this->mRepoWarehouseEntityStockItem
            ->shouldReceive('updateById')->once();
        // $registered = $this->_repoPvMod->getWarehousePv($stockItemRef);
        $this->mRepoPvMod
            ->shouldReceive('getWarehousePv')->once()
            ->andReturn('some data');
        // $this->_repoPvMod->updateWarehousePv($stockItemId, $pv);
        $this->mRepoPvMod
            ->shouldReceive('updateWarehousePv')->once();
        /** === Call and asserts  === */
        $this->obj->updateWarehouseData($STOCK_ITEM_ID, $PRICE, $PV);
    }

    public function test_updateWarehouseData_notExists_notRegistered()
    {
        /** === Test Data === */
        $STOCK_ITEM_ID = 21;
        $PRICE = 43.32;
        $PV = 54.32;
        /** === Setup Mocks === */
        // $exist = $this->_repoWarehouseEntityStockItem->getById($stockItemRef);
        $this->mRepoWarehouseEntityStockItem
            ->shouldReceive('getById')->once()
            ->andReturn(null);
        // $this->_repoWarehouseEntityStockItem->create($bind);
        $this->mRepoWarehouseEntityStockItem
            ->shouldReceive('create')->once();
        // $registered = $this->_repoPvMod->getWarehousePv($stockItemRef);
        $this->mRepoPvMod
            ->shouldReceive('getWarehousePv')->once()
            ->andReturn(null);
        // $this->_repoPvMod->registerWarehousePv($stockItemRef, $pv);
        $this->mRepoPvMod
            ->shouldReceive('registerWarehousePv')->once();
        /** === Call and asserts  === */
        $this->obj->updateWarehouseData($STOCK_ITEM_ID, $PRICE, $PV);
    }
}