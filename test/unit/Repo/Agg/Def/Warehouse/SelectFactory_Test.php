<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def\Warehouse;

use Praxigento\Warehouse\Repo\Agg\Def\Warehouse as WrhsRepoAggWarehouse;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class SelectFactory_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mRepoWrhsAggWarehouse;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  SelectFactory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mConn = $this->_mockConn();
        $this->mResource = $this->_mockResourceConnection($this->mConn);
        $this->mRepoWrhsAggWarehouse = $this->_mock(WrhsRepoAggWarehouse::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new SelectFactory(
            $this->mResource,
            $this->mRepoWrhsAggWarehouse
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(SelectFactory::class, $this->obj);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Setup Mocks === */
        // $result = $this->_repoWrhsAggWarehouse->getQueryToSelectCount();
        $mQuery = $this->_mockDbSelect();
        $this->mRepoWrhsAggWarehouse
            ->shouldReceive('getQueryToSelectCount')->once()
            ->andReturn($mQuery);
        // $tblOdoo = [$asOdoo => $this->_resource->getTableName(EntityWarehouse::ENTITY_NAME)];
        $this->mResource
            ->shouldReceive('getTableName')->once();
        // $result->joinLeft($tblOdoo, $on, $cols);
        $mQuery->shouldReceive('joinLeft')->once();
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertTrue($res instanceof \Magento\Framework\DB\Select);
    }

    public function test_getQueryToSelect()
    {
        /** === Setup Mocks === */
        // $result = $this->_repoWrhsAggWarehouse->getQueryToSelectCount();
        $mQuery = $this->_mockDbSelect();
        $this->mRepoWrhsAggWarehouse
            ->shouldReceive('getQueryToSelect')->once()
            ->andReturn($mQuery);
        // $tblOdoo = [$asOdoo => $this->_resource->getTableName(EntityWarehouse::ENTITY_NAME)];
        $this->mResource
            ->shouldReceive('getTableName')->once();
        // $result->joinLeft($tblOdoo, $on, $cols);
        $mQuery->shouldReceive('joinLeft')->once();
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelect();
        $this->assertTrue($res instanceof \Magento\Framework\DB\Select);
    }

}