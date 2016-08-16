<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Odoo\Inventory;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Option_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  Option */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new Option();
    }

    public function test_accessors()
    {
        /** === Test Data === */
        $CURRENCY = 'CUR';
        /** === Call and asserts  === */
        $this->obj->setCurrency($CURRENCY);
        $this->assertEquals($CURRENCY, $this->obj->getCurrency());
    }

}