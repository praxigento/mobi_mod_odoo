<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def;

use Praxigento\Odoo\Repo\Agg\Data\Lot as AggLot;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Lot_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo
{
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
    /** @var  Lot */
    private $obj;
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mManTrans = $this->_mockTransactionManager();
        $this->mRepoWrhsEntityLot = $this->_mock(\Praxigento\Warehouse\Repo\Entity\Def\Lot::class);
        $this->mRepoEntityLot = $this->_mock(\Praxigento\Odoo\Repo\Entity\Lot::class);
        $this->mFactorySelect = $this->_mock(Lot\SelectFactory::class);
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $this->mManObj,
            $this->mManTrans,
            $this->mResource,
            $this->mRepoWrhsEntityLot,
            $this->mRepoEntityLot,
            $this->mFactorySelect
        ];
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

    public function test__checkNullLot()
    {
        /** === Test Data === */
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(
            \Praxigento\Odoo\Repo\Agg\Store\Lot::class . '[create]',
            $this->objArgs
        );
        /** === Setup Mocks === */
        // $data = $this->_repoEntityLot->getByOdooId(AggLot::NULL_LOT_ID);
        $this->mRepoEntityLot
            ->shouldReceive('getByOdooId')->once()
            ->andReturn(null);
        // $this->create($data);
        $this->obj
            ->shouldReceive('create')->once();
        /** === Call and asserts  === */
        $this->obj->_checkNullLot();
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
        // $def = $this->_manTrans->begin();
        $mDef = $this->_mockTransactionDefinition();
        $this->mManTrans
            ->shouldReceive('begin')->once()
            ->andReturn($mDef);
        // $id = $this->_repoWrhsEntityRepoLot->create($bind);
        $this->mRepoWrhsEntityLot
            ->shouldReceive('create')->once()
            ->andReturn($MAGE_ID);
        // $this->_repoEntityLot->create($bind);
        $this->mRepoEntityLot
            ->shouldReceive('create')->once();
        // $this->_manTrans->commit($def);
        $this->mManTrans
            ->shouldReceive('commit')->once();
        // $result = $this->_manObj->create(AggLot::class);
        $this->mManObj->shouldReceive('create')->once()
            ->andReturn(new AggLot());
        // $this->_manTrans->end($def);
        $this->mManTrans
            ->shouldReceive('end')->once();
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
        // $query = $this->_factorySelect->getQueryToSelect();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelect')->once()
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
        $ODOO_ID = null;
        $DATA = [AggLot::AS_ODOO_ID => $ODOO_ID];
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(
            \Praxigento\Odoo\Repo\Agg\Store\Lot::class . '[_checkNullLot]',
            $this->objArgs
        );
        /** === Setup Mocks === */
        // $this->_checkNullLot();
        $this->obj
            ->shouldReceive('_checkNullLot')->once();
        //
        // $query = $this->_factorySelect->getQueryToSelect();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelect')->once()
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

    public function test_getMageIdByOdooId()
    {
        /** === Test Data === */
        $ODOO_ID = null;
        $RESULT = 'result';
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(
            \Praxigento\Odoo\Repo\Agg\Store\Lot::class . '[_checkNullLot]',
            $this->objArgs
        );
        /** === Setup Mocks === */
        $this->obj
            ->shouldReceive('_checkNullLot')->once();
        // $result = $this->_repoEntityLot->getMageIdByOdooId($id);
        $this->mRepoEntityLot
            ->shouldReceive('getMageIdByOdooId')->once()
            ->with($ODOO_ID)
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getMageIdByOdooId($ODOO_ID);
        $this->assertEquals($RESULT, $res);
    }

    public function test_getQueryToSelect()
    {
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getQueryToSelect();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelect')->once()
            ->andReturn($mQuery);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelect();
        $this->assertInstanceOf(\Magento\Framework\DB\Select::class, $res);
    }

    public function test_getQueryToSelectCount()
    {
        /** === Setup Mocks === */
        // $result = $this->_factorySelect->getQueryToSelectCount();
        $mQuery = $this->_mockDbSelect();
        $this->mFactorySelect
            ->shouldReceive('getQueryToSelectCount')->once()
            ->andReturn($mQuery);
        /** === Call and asserts  === */
        $res = $this->obj->getQueryToSelectCount();
        $this->assertInstanceOf(\Magento\Framework\DB\Select::class, $res);
    }

}