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
        /** create object to test */
        $this->obj = new Warehouse(
            $this->mManObj,
            $this->mMageRepoStockItem,
            $this->mRepoRegistry,
            $this->mRepoPvMod,
            $this->mRepoWarehouseEntityStockItem,
            $this->mRepoPvStockItem,
            $this->mSubLot
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Warehouse::class, $this->obj);
    }

}