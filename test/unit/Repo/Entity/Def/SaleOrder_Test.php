<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Entity\SaleOrder as Entity;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class SaleOrder_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Repo\Entity
{
    /** @var  SaleOrder */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new SaleOrder(
            $this->mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(SaleOrder::class, $this->obj);
    }

    public function test_getIdsToSaveToOdoo()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        // $tblSaleOrder = [$asSaleOrder => $this->_resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER)];
        $this->mResource
            ->shouldReceive('getTableName')->once()
            ->with(Cfg::ENTITY_MAGE_SALES_ORDER)
            ->andReturn('mage_order');
        // $tblOdooReg = [$asOdooReg => $this->_resource->getTableName(Entity::ENTITY_NAME)];
        $this->mResource
            ->shouldReceive('getTableName')->once()
            ->with(Entity::ENTITY_NAME)
            ->andReturn('prxgt_order');
        // $query = $this->_conn->select();
        $mSelect = $this->_mockDbSelect(['from', 'joinLeft', 'where']);
        $this->mConn
            ->shouldReceive('select')->once()
            ->andReturn($mSelect);
        // $result = $this->_conn->fetchAll($query);
        $mResult = ['result'];
        $this->mConn
            ->shouldReceive('fetchAll')->once()
            ->with($mSelect)
            ->andReturn($mResult);
        /** === Call and asserts  === */
        $res = $this->obj->getIdsToSaveToOdoo();
    }
}