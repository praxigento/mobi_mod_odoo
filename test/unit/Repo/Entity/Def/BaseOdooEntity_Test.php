<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Entity\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class BaseOdooEntityToTest extends BaseOdooEntity
{
}

class BaseOdooEntity_UnitTest extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  BaseOdooEntity */
    private $obj;
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];

    protected function setUp()
    {
        parent::setUp();
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $this->mResource,
            $this->mRepoGeneric,
            \Praxigento\Odoo\Data\Entity\Warehouse::class // use Warehouse as base entity
        ];
        /** create object to test */
        $this->obj = new BaseOdooEntityToTest(
            $this->mResource,
            $this->mRepoGeneric,
            \Praxigento\Odoo\Data\Entity\Warehouse::class // use Warehouse as base entity
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(BaseOdooEntity::class, $this->obj);
    }

    public function test_getByOdooId()
    {
        /** === Test Data === */
        $ID = 4;
        $ITEMS = [[]];
        /** === Setup Mocks === */
        // $items = $this->_repoGeneric->getEntities($this->_entityName, null, $where);
        $this->mRepoGeneric
            ->shouldReceive('getEntities')->once()
            ->andReturn($ITEMS);
        /** === Call and asserts  === */
        $res = $this->obj->getByOdooId($ID);
        $this->assertTrue($res instanceof \Praxigento\Odoo\Data\Entity\Warehouse);
    }

    public function test_getMageIdByOdooId()
    {
        /** === Test Data === */
        $ODOO_ID = 4;
        $MAGE_ID = 16;
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(BaseOdooEntityToTest::class . '[getByOdooId]', $this->objArgs);
        /** === Setup Mocks === */
        // $item = $this->getByOdooId($id);
        $mItem = $this->_mock(\Praxigento\Odoo\Data\Entity\Warehouse::class);
        $this->obj
            ->shouldReceive('getByOdooId')->once()
            ->andReturn($mItem);
        // $result = $item->getMageRef();
        $mItem->shouldReceive('getMageRef')->once()
            ->andReturn($MAGE_ID);
        /** === Call and asserts  === */
        $res = $this->obj->getMageIdByOdooId($ODOO_ID);
        $this->assertEquals($MAGE_ID, $res);
    }
}