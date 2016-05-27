<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Def;

use Praxigento\Pv\Data\Entity\Stock\Item as EntityPvStockItem;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Pv_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mRepoPvProduct;
    /** @var  \Mockery\MockInterface */
    private $mRepoPvStockItem;
    /** @var  Pv */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mRepoPvProduct = $this->_mock(\Praxigento\Pv\Repo\Entity\IProduct::class);
        $this->mRepoPvStockItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Stock\IItem::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Pv(
            $this->mRepoPvProduct,
            $this->mRepoPvStockItem
        );
    }

    public function test_getWarehousePv()
    {
        /** === Test Data === */
        $STOCK_ITEM_ID = 21;
        $PV = 43;
        $DATA = [
            EntityPvStockItem::ATTR_PV => $PV
        ];
        /** === Setup Mocks === */
        // $data = $this->_repoPvStockItem->getById($stockItemMageId);
        $this->mRepoPvStockItem
            ->shouldReceive('getById')->once()
            ->andReturn($DATA);
        /** === Call and asserts  === */
        $res = $this->obj->getWarehousePv($STOCK_ITEM_ID);
        $this->assertEquals($PV, $res);
    }

    public function test_registerProductWholesalePv()
    {
        /** === Test Data === */
        $ID = 21;
        $PV = 34.23;
        /** === Setup Mocks === */
        $this->mRepoPvProduct
            ->shouldReceive('create')->once();
        /** === Call and asserts  === */
        $this->obj->registerProductWholesalePv($ID, $PV);
    }

    public function test_registerWarehousePv()
    {
        /** === Test Data === */
        $ID = 21;
        $PV = 34.23;
        /** === Setup Mocks === */
        $this->mRepoPvStockItem
            ->shouldReceive('create')->once();
        /** === Call and asserts  === */
        $this->obj->registerWarehousePv($ID, $PV);
    }

    public function test_updateProductWholesalePv()
    {
        /** === Test Data === */
        $ID = 21;
        $PV = 34.23;
        /** === Setup Mocks === */
        $this->mRepoPvProduct
            ->shouldReceive('update')->once();
        /** === Call and asserts  === */
        $this->obj->updateProductWholesalePv($ID, $PV);
    }

    public function test_updateWarehousePv()
    {
        /** === Test Data === */
        $ID = 21;
        $PV = 34.23;
        /** === Setup Mocks === */
        $this->mRepoPvStockItem
            ->shouldReceive('update')->once();
        /** === Call and asserts  === */
        $this->obj->updateWarehousePv($ID, $PV);
    }

}