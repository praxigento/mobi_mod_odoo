<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Observer;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class CheckoutSubmitAllAfter_UnitTest extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Mockery\MockInterface */
    private $mCallReplicate;
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mManStock;
    /** @var  CheckoutSubmitAllAfter */
    private $obj;

    public function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mLogger = $this->_mockLogger();
        $this->mManStock = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        $this->mCallReplicate = $this->_mock(\Praxigento\Odoo\Service\IReplicate::class);
        /** create object to test */
        $this->obj = new CheckoutSubmitAllAfter(
            $this->mLogger,
            $this->mManStock,
            $this->mCallReplicate
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(\Praxigento\Odoo\Observer\CheckoutSubmitAllAfter::class, $this->obj);
    }

    public function test_execute_success()
    {
        /** === Test Data === */
        $STATE = \Magento\Sales\Model\Order::STATE_PROCESSING;
        $mObserver = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** === Setup Mocks === */
        // $order = $observer->getData(self::DATA_ORDER);
        $mOrder = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $mObserver
            ->shouldReceive('getData')->once()
            ->with(CheckoutSubmitAllAfter::DATA_ORDER)
            ->andReturn($mOrder);
        // $state = $order->getState();
        $mOrder->shouldReceive('getState')->once()
            ->andReturn($STATE);
        // $this->_callReplicate->orderSave($req);
        $this->mCallReplicate
            ->shouldReceive('orderSave')->once();
        /** === Call and asserts  === */
        $this->obj->execute($mObserver);
    }

    public function test_execute_exception()
    {
        /** === Test Data === */
        $STATE = \Magento\Sales\Model\Order::STATE_PROCESSING;
        $mObserver = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** === Setup Mocks === */
        // $order = $observer->getData(self::DATA_ORDER);
        $mOrder = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $mObserver
            ->shouldReceive('getData')->once()
            ->with(CheckoutSubmitAllAfter::DATA_ORDER)
            ->andReturn($mOrder);
        // $state = $order->getState();
        $mOrder->shouldReceive('getState')->once()
            ->andReturn($STATE);
        // $this->_callReplicate->orderSave($req);
        $this->mCallReplicate
            ->shouldReceive('orderSave')->once()
            ->andThrow(new \Exception());
        /** === Call and asserts  === */
        $this->obj->execute($mObserver);
    }
}