<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Def;

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
        /* create mocks */
        $this->mRepoPvProduct = $this->_mock(\Praxigento\Pv\Repo\Entity\IProduct::class);
        $this->mRepoPvStockItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Stock\IItem::class);
        /* setup mocks for constructor */
        /* create object to test */
        $this->obj = new Pv(
            $this->mRepoPvProduct,
            $this->mRepoPvStockItem
        );
    }

    public function test_registerProductWholesalePv()
    {
        /* === Test Data === */
        $ID = 21;
        $PV = 34.23;
        /* === Setup Mocks === */
        $this->mRepoPvProduct
            ->shouldReceive('create')->once();
        /* === Call and asserts  === */
        $this->obj->registerProductWholesalePv($ID, $PV);
    }

    public function test_registerWarehousePv()
    {
        /* === Test Data === */
        $ID = 21;
        $PV = 34.23;
        /* === Setup Mocks === */
        $this->mRepoPvStockItem
            ->shouldReceive('create')->once();
        /* === Call and asserts  === */
        $this->obj->registerWarehousePv($ID, $PV);
    }

    public function test_updateProductWholesalePv()
    {
        /* === Test Data === */
        $ID = 21;
        $PV = 34.23;
        /* === Setup Mocks === */
        $this->mRepoPvProduct
            ->shouldReceive('update')->once();
        /* === Call and asserts  === */
        $this->obj->updateProductWholesalePv($ID, $PV);
    }

    public function test_updateWarehousePv()
    {
        /* === Test Data === */
        $ID = 21;
        $PV = 34.23;
        /* === Setup Mocks === */
        $this->mRepoPvStockItem
            ->shouldReceive('update')->once();
        /* === Call and asserts  === */
        $this->obj->updateWarehousePv($ID, $PV);
    }

}