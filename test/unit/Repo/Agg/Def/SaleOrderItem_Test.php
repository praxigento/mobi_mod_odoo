<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class SaleOrderItem_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mFactorySelect;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  Lot */
    private $obj;
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn();
        $this->mResource = $this->_mockResourceConnection($this->mConn);
        $this->mFactorySelect = $this->_mock(SaleOrderItem\SelectFactory::class);
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $this->mResource,
            $this->mFactorySelect
        ];
        /** create object to test */
        $this->obj = new SaleOrderItem(
            $this->mResource,
            $this->mFactorySelect
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(SaleOrderItem::class, $this->obj);
    }

    public function test_getQueryToSelect()
    {
        /** === Test Data === */
        $RESULT = 'result';
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getQueryToSelect();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelect')->once()
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelect();
        $this->assertEquals($RESULT, $res);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Test Data === */
        $RESULT = 'result';
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getQueryToSelectCount();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelectCount')->once()
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertEquals($RESULT, $res);
    }

    public function test_getByOrderAndStock()
    {
        /** === Test Data === */
        $ORDER_ID = 16;
        $STOCK_ID = 2;
        $DATA = [[], []];
        /** === Setup Mocks === */
        // $select = $this->_factorySelect->getQueryToSelect();
        $mSelect = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelect')->once()
            ->andReturn($mSelect);
        // $data = $this->_conn->fetchAll($select, $bind);
        $this->mConn
            ->shouldReceive('fetchAll')->once()
            ->andReturn($DATA);
        /** === Call and asserts  === */
        $res = $this->obj->getByOrderAndStock($ORDER_ID, $STOCK_ID);
        $this->assertTrue(is_array($res));
        $item = reset($res);
        $this->assertTrue($item instanceof \Praxigento\Odoo\Data\Agg\SaleOrderItem);
    }
}