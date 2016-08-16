<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Observer;

include_once(__DIR__ . '/../phpunit_bootstrap.php');

class SalesOrderInvoicePay_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
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
        $this->obj = new SalesOrderInvoicePay(
            $this->mLogger,
            $this->mManStock,
            $this->mCallReplicate
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(\Praxigento\Odoo\Observer\SalesOrderInvoicePay::class, $this->obj);
    }

    public function test_execute_success()
    {
        /** === Test Data === */
        $STATE = \Magento\Sales\Model\Order\Invoice::STATE_PAID;
        $mObserver = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** === Setup Mocks === */
        // $invoice = $observer->getData(self::DATA_INVOICE);
        $mInvoice = $this->_mock(\Magento\Sales\Model\Order\Invoice::class);
        $mObserver
            ->shouldReceive('getData')->once()
            ->with(SalesOrderInvoicePay::DATA_INVOICE)
            ->andReturn($mInvoice);
        // $state = $invoice->getState();
        $mInvoice->shouldReceive('getState')->once()
            ->andReturn($STATE);
        // $order = $invoice->getOrder();
        $mOrder = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $mInvoice
            ->shouldReceive('getOrder')->once()
            ->andReturn($mOrder);
        // $this->_callReplicate->orderSave($req);
        $this->mCallReplicate
            ->shouldReceive('orderSave')->once();
        /** === Call and asserts  === */
        $this->obj->execute($mObserver);
    }

    public function test_execute_exception()
    {
        /** === Test Data === */
        $STATE = \Magento\Sales\Model\Order\Invoice::STATE_PAID;
        $mObserver = $this->_mock(\Magento\Framework\Event\Observer::class);
        /** === Setup Mocks === */
        // $invoice = $observer->getData(self::DATA_INVOICE);
        $mInvoice = $this->_mock(\Magento\Sales\Model\Order\Invoice::class);
        $mObserver
            ->shouldReceive('getData')->once()
            ->with(SalesOrderInvoicePay::DATA_INVOICE)
            ->andReturn($mInvoice);
        // $state = $invoice->getState();
        $mInvoice->shouldReceive('getState')->once()
            ->andReturn($STATE);
        // $order = $invoice->getOrder();
        $mOrder = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $mInvoice
            ->shouldReceive('getOrder')->once()
            ->andReturn($mOrder);
        // $this->_callReplicate->orderSave($req);
        $this->mCallReplicate
            ->shouldReceive('orderSave')->once()
            ->andThrow(new \Exception());
        /** === Call and asserts  === */
        $this->obj->execute($mObserver);
    }
}