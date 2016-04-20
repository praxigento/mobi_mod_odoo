<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Api\Bundle\Def;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');


class Option_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  Option */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create object to test */
        $this->obj = new Option();
    }

    public function test_accessors()
    {
        /* === Test Data === */
        $CUR = 'currency';
        /* === Call and asserts  === */
        $this->obj->setCurrency($CUR);
        $this->assertEquals($CUR, $this->obj->getCurrency());
    }

}