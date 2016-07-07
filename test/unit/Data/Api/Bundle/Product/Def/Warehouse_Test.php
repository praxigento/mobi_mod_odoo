<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Odoo\Inventory\Product\Def;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');


class Warehouse_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Warehouse */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Warehouse();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $ID = 'id';
        $LOTS = 'lots';
        $PRICE = 'price';
        $PV = 'pv';
        /** === Call and asserts  === */
        $this->obj->setId($ID);
        $this->obj->setLots($LOTS);
        $this->obj->setPrice($PRICE);
        $this->obj->setPv($PV);
        $this->assertEquals($ID, $this->obj->getId());
        $this->assertEquals($LOTS, $this->obj->getLots());
        $this->assertEquals($PRICE, $this->obj->getPrice());
        $this->assertEquals($PV, $this->obj->getPv());
    }

}