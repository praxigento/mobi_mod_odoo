<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Api\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');


class Bundle_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Bundle */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Bundle();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $CATEGORIES = 'categories';
        $LOTS = 'lots';
        $OPTION = 'option';
        $PRODUCTS = 'products';
        $WAREHOUSES = 'warehouses';
        /** === Call and asserts  === */
        $this->obj->setCategories($CATEGORIES);
        $this->obj->setLots($LOTS);
        $this->obj->setOption($OPTION);
        $this->obj->setProducts($PRODUCTS);
        $this->obj->setWarehouses($WAREHOUSES);
        $this->assertEquals($CATEGORIES, $this->obj->getCategories());
        $this->assertEquals($LOTS, $this->obj->getLots());
        $this->assertEquals($OPTION, $this->obj->getOption());
        $this->assertEquals($PRODUCTS, $this->obj->getProducts());
        $this->assertEquals($WAREHOUSES, $this->obj->getWarehouses());
    }

}