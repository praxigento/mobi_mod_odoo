<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Odoo\Inventory\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');


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
        $CODE = 'code';
        $CUR = 'currency';
        $ID = 'id';
        /** === Call and asserts  === */
        $this->obj->setCode($CODE);
        $this->obj->setCurrency($CUR);
        $this->obj->setIdOdoo($ID);
        $this->assertEquals($CODE, $this->obj->getCode());
        $this->assertEquals($CUR, $this->obj->getCurrency());
        $this->assertEquals($ID, $this->obj->getIdOdoo());
    }

}