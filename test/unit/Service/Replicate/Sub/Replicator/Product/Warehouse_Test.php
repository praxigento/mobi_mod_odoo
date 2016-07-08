<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class Warehouse_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
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
    private $mSubDataHandler;
    /** @var  \Mockery\MockInterface */
    private $mSubLot;
    /** @var  Warehouse */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mMageRepoStockItem = $this->_mock(\Magento\CatalogInventory\Api\StockItemRepositoryInterface::class);
        $this->mRepoRegistry = $this->_mock(\Praxigento\Odoo\Repo\IRegistry::class);
        $this->mRepoPvMod = $this->_mock(\Praxigento\Odoo\Repo\IPv::class);
        $this->mRepoWarehouseEntityStockItem = $this->_mock(\Praxigento\Warehouse\Repo\Entity\Stock\IItem::class);
        $this->mRepoPvStockItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Stock\IItem::class);
        $this->mSubLot = $this->_mock(\Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Lot::class);
        $this->mSubDataHandler = $this->_mock(Warehouse\DataHandler::class);
        /** create object to test */
        $this->obj = new Warehouse(
            $this->mManObj,
            $this->mMageRepoStockItem,
            $this->mRepoRegistry,
            $this->mSubLot,
            $this->mSubDataHandler
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Warehouse::class, $this->obj);
    }


    public function test_processLot()
    {
        /** === Test Data === */
        $PRODUCT_ID = 32;
        /* this warehouse exists in Mage */
        $WRHS1_ID_ODOO = 401;
        $WRHS1_ID_MAGE = 104;
        $WRHS1_PV = 545;
        $WRHS1_PRICE = 12.23;
        // lots
        $WRHS1_LOT1_QTY = 10;
        $WRHS1_LOTS = [1];
        $WRHS_1 = new \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse();
        $WRHS_1->setIdOdoo($WRHS1_ID_ODOO);
        $WRHS_1->setPvWarehouse($WRHS1_PV);
        $WRHS_1->setPriceWarehouse($WRHS1_PRICE);
        $WRHS_1->setLots($WRHS1_LOTS);
        /* this warehouse is missed in Mage */
        $WRHS2_ID_ODOO = 402;
        $WRHS2_PV = 546;
        $WRHS2_PRICE = 21.23;
        $WRHS2_LOTS = [];
        $WRHS_2 = new \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse();
        $WRHS_2->setIdOdoo($WRHS2_ID_ODOO);
        $WRHS_2->setPvWarehouse($WRHS2_PV);
        $WRHS_2->setPriceWarehouse($WRHS2_PRICE);
        $WRHS_2->setLots($WRHS2_LOTS);
        /* compose warehouses list */
        $WAREHOUSES = [$WRHS_1, $WRHS_2];
        $STOCK_ITEM_ID_1 = 501;
        /** === Setup Mocks === */
        //
        // $stockItems = $this->_getStockItems($productIdMage);
        //
        // $crit = $this->_manObj->create(StockItemCriteriaInterface::class);
        $mCrit = $this->_mock(\Magento\CatalogInventory\Api\StockItemCriteriaInterface::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mCrit);
        // $crit->setProductsFilter($prodId);
        $mCrit->shouldReceive('setProductsFilter')->once();
        // $list = $this->_mageRepoStockItem->getList($crit);
        $mList = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemCollectionInterface::class);
        $this->mMageRepoStockItem
            ->shouldReceive('getList')->once()
            ->andReturn($mList);
        // $result = $list->getItems();
        $mStockItem1 = $this->_mock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $mStockItems = [$STOCK_ITEM_ID_1 => $mStockItem1];
        $mList->shouldReceive('getItems')->once()
            ->andReturn($mStockItems);
        //
        // $mapItemsByStock = $this->_mapStockIds($stockItems);
        //
        // foreach ($stockItems as $stockItemId => $stockItem) {}
        // $stockId = $stockItem->getStockId();
        $mStockItem1->shouldReceive('getStockId')->once()
            ->andReturn($WRHS1_ID_MAGE);
        // $stockIdMage = $this->_repoRegistry->getWarehouseMageIdByOdooId($stockIdOdoo);
        $this->mRepoRegistry
            ->shouldReceive('getWarehouseMageIdByOdooId')->once()
            ->with($WRHS1_ID_ODOO)
            ->andReturn($WRHS1_ID_MAGE);
        //
        // First loop (wrhs exists)
        // $this->_subDataHandler->updateWarehouseData($stockItemIdMage, $priceWarehouse, $pvWarehouse);
        $this->mSubDataHandler
            ->shouldReceive('updateWarehouseData')->once();
        // $this->_subDataHandler->processLots($lots, $stockItem);
        $this->mSubDataHandler
            ->shouldReceive('processLots')->once();
        //
        // Second loop (wrhs does not exist)
        //
        // $stockIdMage = $this->_repoRegistry->getWarehouseMageIdByOdooId($stockIdOdoo);
        $this->mRepoRegistry
            ->shouldReceive('getWarehouseMageIdByOdooId')->once()
            ->with($WRHS2_ID_ODOO)
            ->andReturn(null);
        // $stockItem = $this->_subDataHandler->createWarehouseData($productIdMage, $stockIdMage, $priceWarehouse, $pvWarehouse);
        $this->mSubDataHandler
            ->shouldReceive('createWarehouseData')->once();
        /** === Call and asserts  === */
        $this->obj->processWarehouses($PRODUCT_ID, $WAREHOUSES);
    }
}