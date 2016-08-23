<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def\SaleOrderItem;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class SelectFactory_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo
{
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  SelectFactory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        /** create object to test */
        $this->obj = new SelectFactory(
            $this->mLogger,
            $this->mResource
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(SelectFactory::class, $this->obj);
    }

    public function test_getQueryToSelect()
    {
        /** === Setup Mocks === */
        // $result = $this->_conn->select();
        $mResult = $this->_mockDbSelect(['from', 'joinLeft', 'where']);
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mResult);
        // ... $this->_resource->getTableName(...)
        $this->mResource
            ->shouldReceive('getTableName');
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelect();
        $this->assertTrue($res instanceof \Magento\Framework\DB\Select);
    }

    /**
     * @expectedException \Exception
     */
    public function test_getQueryToSelectCount()
    {
        /** === Call and asserts  === */
        $this->obj->getQueryToSelectCount();
    }

}