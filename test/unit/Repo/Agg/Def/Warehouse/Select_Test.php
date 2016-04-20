<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def\Warehouse;

use Praxigento\Warehouse\Repo\Agg\Def\Warehouse as WrhsRepoAggWarehouse;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Select_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mRepoWrhsAggWarehouse;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  Select */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mConn = $this->_mockConn();
        $this->mResource = $this->_mockResourceConnection($this->mConn);
        $this->mRepoWrhsAggWarehouse = $this->_mock(WrhsRepoAggWarehouse::class);
        /* setup mocks for constructor */
        /* create object to test */
        $this->obj = new Select(
            $this->mResource,
            $this->mRepoWrhsAggWarehouse
        );
    }

    public function test_constructor()
    {
        /* === Test Data === */
        /* === Setup Mocks === */
        /* === Call and asserts  === */
        $this->assertInstanceOf(Select::class, $this->obj);
    }


    public function test_getQuery()
    {
        /* === Test Data === */
        /* === Setup Mocks === */
        // $result = $this->_repoWrhsWarehouse->getQueryToSelect();
        $mQuery = $this->_mockDbSelect();
        $this->mRepoWrhsAggWarehouse
            ->shouldReceive('getQueryToSelect')->once()
            ->andReturn($mQuery);
        // $tblOdoo = [$asOdoo => $this->_conn->getTableName(EntityWarehouse::ENTITY_NAME)];
        $this->mConn
            ->shouldReceive('getTableName')->once();
        // $result->joinLeft($tblOdoo, $on, $cols);
        $mQuery->shouldReceive('joinLeft')->once();
        /* === Call and asserts  === */
        $this->obj->getQuery();
    }

}