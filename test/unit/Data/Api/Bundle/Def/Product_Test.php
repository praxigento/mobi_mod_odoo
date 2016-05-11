<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Api\Bundle\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');


class Product_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Product */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Product();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $CATEGORIES = 'categories';
        $ID = 'id';
        $IS_ACTIVE = 'is active';
        $NAME = 'name';
        $PRICE = 'price';
        $PV = 'pv';
        $SKU = 'sku';
        $WAREHOUSE = 'warehouse';
        $WEIGHT = 'weight';
        /** === Call and asserts  === */
        $this->obj->setCategories($CATEGORIES);
        $this->obj->setId($ID);
        $this->obj->setIsActive($IS_ACTIVE);
        $this->obj->setName($NAME);
        $this->obj->setPrice($PRICE);
        $this->obj->setPv($PV);
        $this->obj->setSku($SKU);
        $this->obj->setWarehouses($WAREHOUSE);
        $this->obj->setWeight($WEIGHT);
        $this->assertEquals($CATEGORIES, $this->obj->getCategories());
        $this->assertEquals($ID, $this->obj->getId());
        $this->assertEquals($IS_ACTIVE, $this->obj->getIsActive());
        $this->assertEquals($NAME, $this->obj->getName());
        $this->assertEquals($PRICE, $this->obj->getPrice());
        $this->assertEquals($PV, $this->obj->getPv());
        $this->assertEquals($SKU, $this->obj->getSku());
        $this->assertEquals($WAREHOUSE, $this->obj->getWarehouses());
        $this->assertEquals($WEIGHT, $this->obj->getWeight());
    }

}