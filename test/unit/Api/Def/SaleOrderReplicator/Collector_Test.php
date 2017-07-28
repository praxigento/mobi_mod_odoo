<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Def\SaleOrderReplicator;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

use Praxigento\Odoo\Config as Cfg;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class Collector_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mRepoMageSaleOrder;
    /** @var  \Mockery\MockInterface */
    private $mRepoSaleOrder;
    /** @var  Collector */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mRepoMageSaleOrder = $this->_mock(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $this->mRepoSaleOrder = $this->_mock(\Praxigento\Odoo\Repo\Entity\Def\SaleOrder::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new Collector(
            $this->mRepoMageSaleOrder,
            $this->mRepoSaleOrder
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Collector::class, $this->obj);
    }

    public function test_getOrdersToReplicate()
    {
        /** === Test Data === */
        /** === Setup Mocks === */
        // $orders = $this->_repoSaleOrder->getIdsToSaveToOdoo();
        $mData = [];
        $mId = 32;
        $mData[Cfg::E_SALE_ORDER_A_ENTITY_ID] = $mId;
        $mOrders = [$mData];
        $this->mRepoSaleOrder
            ->shouldReceive('getIdsToSaveToOdoo')->once()
            ->andReturn($mOrders);
        // $id = $data[Cfg::E_SALE_ORDER_A_ENTITY_ID];
        // $order = $this->_repoMageSalesOrder->get($id);
        $mOrder = 'order';
        $this->mRepoMageSaleOrder
            ->shouldReceive('get')->once()
            ->with($mId)
            ->andReturn($mOrder);
        /** === Call and asserts  === */
        $res = $this->obj->getOrdersToReplicate();
        $this->assertEquals($mOrder, $res[$mId]);
    }
}