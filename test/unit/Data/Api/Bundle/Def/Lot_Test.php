<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Odoo\Inventory\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');


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
        $CODE = 'code';
        $EXP_DATE = 'exp date';
        $ID = 'id';
        /** === Call and asserts  === */
        $this->obj->setNumber($CODE);
        $this->obj->setExpirationDate($EXP_DATE);
        $this->obj->setIdOdoo($ID);
        $this->assertEquals($CODE, $this->obj->getNumber());
        $this->assertEquals($EXP_DATE, $this->obj->getExpirationDate());
        $this->assertEquals($ID, $this->obj->getIdOdoo());
    }

}