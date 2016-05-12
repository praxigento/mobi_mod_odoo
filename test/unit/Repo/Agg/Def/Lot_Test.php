<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def;

use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Repo\Entity\ILot as IRepoEntityLot;
use Praxigento\Warehouse\Repo\Entity\ILot as IRepoWrhsEntityLot;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Lot_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mConn;
    /** @var  \Mockery\MockInterface */
    private $mFactorySelect;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mManTrans;
    /** @var  \Mockery\MockInterface */
    private $mRepoEntityLot;
    /** @var  \Mockery\MockInterface */
    private $mRepoWrhsEntityLot;
    /** @var  \Mockery\MockInterface */
    private $mResource;
    /** @var  Lot */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mConn = $this->_mockConn();
        $this->mResource = $this->_mockResourceConnection($this->mConn);
        $this->mRepoWrhsEntityLot = $this->_mock(IRepoWrhsEntityLot::class);
        $this->mRepoEntityLot = $this->_mock(IRepoEntityLot::class);
        $this->mFactorySelect = $this->_mock(Lot\SelectFactory::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Lot(
            $this->mManObj,
            $this->mManTrans,
            $this->mResource,
            $this->mRepoWrhsEntityLot,
            $this->mRepoEntityLot,
            $this->mFactorySelect
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Lot::class, $this->obj);
    }

    public function test_create()
    {
        /** === Test Data === */
        $MAGE_ID = 32;
        $ODOO_ID = 54;
        $DATA = new AggLot([AggLot::AS_ODOO_ID => $ODOO_ID]);
        /** === Setup Mocks === */
        // $trans = $this->_manTrans->transactionBegin();
        $mTrans = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('transactionBegin')->once()
            ->andReturn($mTrans);
        // $id = $this->_repoWrhsEntityRepoLot->create($bind);
        $this->mRepoWrhsEntityLot
            ->shouldReceive('create')->once()
            ->andReturn($MAGE_ID);
        // $this->_repoEntityLot->create($bind);
        $this->mRepoEntityLot
            ->shouldReceive('create')->once();
        // $this->_manTrans->transactionCommit($trans);
        $this->mManTrans
            ->shouldReceive('transactionCommit')->once()
            ->with($mTrans);
        // $result = $this->_manObj->create(AggLot::class);
        $this->mManObj->shouldReceive('create')->once()
            ->andReturn(new AggLot());
        // $this->_manTrans->transactionClose($trans);
        $this->mManTrans
            ->shouldReceive('transactionClose')->once()
            ->with($mTrans);
        /** === Call and asserts  === */
        $res = $this->obj->create($DATA);
        $this->assertEquals($MAGE_ID, $res->getId());
    }

    public function test_getById()
    {
        /** === Test Data === */
        $ID = 32;
        $DATA = [AggLot::AS_ID => $ID];
        /** === Setup Mocks === */
        // $query = $this->_factorySelect->getSelectQuery();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getSelectQuery')->once()
            ->andReturn($mQuery);
        // $query->where($where);
        $mQuery->shouldReceive('where')->once();
        // $data = $this->_conn->fetchRow($query, ['id' => $id]);
        $this->mConn
            ->shouldReceive('fetchRow')->once()
            ->andReturn($DATA);
        // $result = $this->_manObj->create(AggWarehouse::class);
        $this->mManObj->shouldReceive('create')->once()
            ->andReturn(new AggLot());
        /** === Call and asserts  === */
        $res = $this->obj->getById($ID);
        $this->assertEquals($ID, $res->getId());
    }

    public function test_getByOdooId()
    {
        /** === Test Data === */
        $ODOO_ID = 32;
        $DATA = [AggLot::AS_ODOO_ID => $ODOO_ID];
        /** === Setup Mocks === */
        // $query = $this->_factorySelect->getSelectQuery();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getSelectQuery')->once()
            ->andReturn($mQuery);
        // $query->where($where);
        $mQuery->shouldReceive('where')->once();
        // $data = $this->_conn->fetchRow($query, ['id' => $id]);
        $this->mConn
            ->shouldReceive('fetchRow')->once()
            ->andReturn($DATA);
        // $result = $this->_manObj->create(AggWarehouse::class);
        $this->mManObj->shouldReceive('create')->once()
            ->andReturn(new AggLot());
        /** === Call and asserts  === */
        $res = $this->obj->getByOdooId($ODOO_ID);
        $this->assertEquals($ODOO_ID, $res->getOdooId());
    }

    public function test_getQueryToSelect()
    {
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getSelectQuery();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getSelectQuery')->once()
            ->andReturn($mQuery);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelect();
        $this->assertInstanceOf(\Magento\Framework\DB\Select::class, $res);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getSelectCountQuery();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getSelectCountQuery')->once()
            ->andReturn($mQuery);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertInstanceOf(\Magento\Framework\DB\Select::class, $res);
    }

}