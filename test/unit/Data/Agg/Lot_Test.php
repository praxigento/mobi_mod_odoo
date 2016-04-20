<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Agg;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');


class Lot_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  Lot */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create object to test */
        $this->obj = new Lot();
    }

    public function test_accessors()
    {
        /* === Test Data === */
        $CODE = 'code';
        $EXP_DATE = 'exp date';
        $ID = 'id';
        $ODOO_ID = 'odoo id';
        /* === Call and asserts  === */
        $this->obj->setCode($CODE);
        $this->obj->setExpDate($EXP_DATE);
        $this->obj->setId($ID);
        $this->obj->setOdooId($ODOO_ID);
        $this->assertEquals($CODE, $this->obj->getCode());
        $this->assertEquals($EXP_DATE, $this->obj->getExpDate());
        $this->assertEquals($ID, $this->obj->getId());
        $this->assertEquals($ODOO_ID, $this->obj->getOdooId());
    }

}