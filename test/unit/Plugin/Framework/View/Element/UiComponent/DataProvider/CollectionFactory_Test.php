<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Plugin\Framework\View\Element\UiComponent\DataProvider;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');

class CollectionFactory_UnitTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mSubQueryModifier;
    /** @var  CollectionFactory */
    private $obj;
    /** @var  \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory */
    private $mSubject;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mSubject = $this->_mock(\Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory::class);
        $this->mSubQueryModifier = $this->_mock(\Praxigento\Odoo\Plugin\Framework\View\Element\UiComponent\DataProvider\Sub\QueryModifier::class);
        /** create object to test */
        $this->obj = new CollectionFactory(
            $this->mSubQueryModifier
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(CollectionFactory::class, $this->obj);
    }

    public function test_aroundGetReport()
    {
        /** === Test Data === */
        $REQUEST_NAME = \Praxigento\Core\Config::DS_SALES_ORDERS_GRID;
        /** === Setup Mocks === */
        $mResult = $this->_mock(\Magento\Sales\Model\ResourceModel\Order\Grid\Collection::class);
        $mProceed = function () use ($mResult) {
            return $mResult;
        };
        // $this->_subQueryModifier->populateSelect($result);
        $this->mSubQueryModifier
            ->shouldReceive('populateSelect')->once();
        // $this->_subQueryModifier->addFieldsMapping($result);
        $this->mSubQueryModifier
            ->shouldReceive('addFieldsMapping')->once();
        /** === Call and asserts  === */
        $res = $this->obj->aroundGetReport(
            $this->mSubject,
            $mProceed,
            $REQUEST_NAME
        );
        $this->assertTrue($res instanceof \Magento\Sales\Model\ResourceModel\Order\Grid\Collection);
    }
}