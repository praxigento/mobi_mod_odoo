<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Def;

use Praxigento\Odoo\Data\Entity\IOdooEntity;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Registry_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mRepoGeneric;
    /** @var  Registry */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mRepoGeneric = $this->_mockRepoGeneric();
        /* setup mocks for constructor */
        /* create object to test */
        $this->obj = new Registry(
            $this->mManObj,
            $this->mRepoGeneric
        );
    }

    public function test_getCategoryMageIdByOdooId()
    {
        /* === Test Data === */
        $ODOO_ID = 21;
        $MAGE_ID = 43;
        /* === Setup Mocks === */
        // $result = $this->_getMageIdByOdooId(EntityCategory::ENTITY_NAME, $odooId);
        // $items = $this->_repoBasic->getEntities($entityName, null, $where);
        $this->mRepoGeneric
            ->shouldReceive('getEntities')->once()
            ->andReturn([[IOdooEntity::ATTR_MAGE_REF => $MAGE_ID]]);
        /* === Call and asserts  === */
        $res = $this->obj->getCategoryMageIdByOdooId($ODOO_ID);
        $this->assertEquals($MAGE_ID, $res);
    }

    public function test_getLotMageIdByOdooId()
    {
        /* === Test Data === */
        $ODOO_ID = 21;
        $MAGE_ID = 43;
        /* === Setup Mocks === */
        // $result = $this->_getMageIdByOdooId(EntityLot::ENTITY_NAME, $odooId);
        // $items = $this->_repoBasic->getEntities($entityName, null, $where);
        $this->mRepoGeneric
            ->shouldReceive('getEntities')->once()
            ->andReturn([[IOdooEntity::ATTR_MAGE_REF => $MAGE_ID]]);
        /* === Call and asserts  === */
        $res = $this->obj->getLotMageIdByOdooId($ODOO_ID);
        $this->assertEquals($MAGE_ID, $res);
    }

    public function test_getProductMageIdByOdooId()
    {
        /* === Test Data === */
        $ODOO_ID = 21;
        $MAGE_ID = 43;
        /* === Setup Mocks === */
        // $result = $this->_getMageIdByOdooId(EntityProduct::ENTITY_NAME, $odooId);
        // $items = $this->_repoBasic->getEntities($entityName, null, $where);
        $this->mRepoGeneric
            ->shouldReceive('getEntities')->once()
            ->andReturn([[IOdooEntity::ATTR_MAGE_REF => $MAGE_ID]]);
        /* === Call and asserts  === */
        $res = $this->obj->getProductMageIdByOdooId($ODOO_ID);
        $this->assertEquals($MAGE_ID, $res);
    }

    public function test_getWarehouseMageIdByOdooId()
    {
        /* === Test Data === */
        $ODOO_ID = 21;
        $MAGE_ID = 43;
        /* === Setup Mocks === */
        // $result = $this->_getMageIdByOdooId(EntityWarehouse::ENTITY_NAME, $odooId);
        // $items = $this->_repoBasic->getEntities($entityName, null, $where);
        $this->mRepoGeneric
            ->shouldReceive('getEntities')->once()
            ->andReturn([[IOdooEntity::ATTR_MAGE_REF => $MAGE_ID]]);
        /* === Call and asserts  === */
        $res = $this->obj->getWarehouseMageIdByOdooId($ODOO_ID);
        $this->assertEquals($MAGE_ID, $res);
    }

    public function test_registerCategory()
    {
        /* === Test Data === */
        $ODOO_ID = 21;
        $MAGE_ID = 43;
        /* === Setup Mocks === */
        // $this->_registerMageIdForOdooId(EntityCategory::ENTITY_NAME, $mageId, $odooId);
        // $this->_repoBasic->addEntity($entityName, $bind);
        $this->mRepoGeneric
            ->shouldReceive('addEntity')->once();
        /* === Call and asserts  === */
        $res = $this->obj->registerCategory($MAGE_ID, $ODOO_ID);
    }

    public function test_registerProduct()
    {
        /* === Test Data === */
        $ODOO_ID = 21;
        $MAGE_ID = 43;
        /* === Setup Mocks === */
        // $this->_registerMageIdForOdooId(EntityProduct::ENTITY_NAME, $mageId, $odooId);
        // $this->_repoBasic->addEntity($entityName, $bind);
        $this->mRepoGeneric
            ->shouldReceive('addEntity')->once();
        /* === Call and asserts  === */
        $res = $this->obj->registerProduct($MAGE_ID, $ODOO_ID);
    }
    public function test_isProductRegisteredInMage()
    {
        /* === Test Data === */
        $ODOO_ID = 21;
        /* === Setup Mocks === */
        // $mageId = $this->getProductMageIdByOdooId($odooId);
        // $result = $this->_getMageIdByOdooId(EntityProduct::ENTITY_NAME, $odooId);
        // $items = $this->_repoBasic->getEntities($entityName, null, $where);
        $this->mRepoGeneric
            ->shouldReceive('getEntities')->once();
        /* === Call and asserts  === */
        $res = $this->obj->isProductRegisteredInMage($ODOO_ID);
    }

}