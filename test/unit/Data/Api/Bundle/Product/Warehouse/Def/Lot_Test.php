<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\Def;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');


class Lot_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Lot */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Lot();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $ID = 'id';
        $QTY = 'quantity';
        /** === Call and asserts  === */
        $this->obj->setId($ID);
        $this->obj->setQuantity($QTY);
        $this->assertEquals($ID, $this->obj->getId());
        $this->assertEquals($QTY, $this->obj->getQuantity());
    }

}