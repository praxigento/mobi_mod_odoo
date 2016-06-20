<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Service\IReplicate;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  IReplicate */
    private $obj;

    protected function setUp()
    {
        $this->manObj = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $this->manObj->create(IReplicate::class);
    }

    public function test_productsFromOdoo()
    {
        $req = new Request\ProductsFromOdoo();
        $resp = $this->obj->productsFromOdoo($req);
        $this->assertNotNull($resp);
    }

    public function test_orderSave()
    {
        $req = new Request\OrderSave();
        $order = new \Praxigento\Odoo\Data\Odoo\SaleOrder();
        $order->setWarehouseId(21);
        $order->setNumber('from mage');
        $order->setDate('2016/06/20 20:18:16');
        $order->setClientId(32);
        /* billing address */
        $contact = new \Praxigento\Odoo\Data\Odoo\Contact();
        $contact->setName('name');
        //
        $order->setAddrBilling($contact);
        $order->setAddrShipping($contact);
        $order->setShippingMethod('shipping');
        $order->setShippingPrice(1.21);
        $order->setPriceDiscountAdditional(0);
        $order->setPriceTax(2.22);
        /* lines */
        $line = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line();
        $line->setLotId(43);
        $line->setPriceActual(32.21);
        $line->setPriceAdjusted(30.00);
        $line->setPriceDiscount(5.55);
        $line->setPvActual(44.44);
        $line->setPvDiscount(4.44);
        //
        $order->setLines([$line]);
        /* payments */
        $payment = new \Praxigento\Odoo\Data\Odoo\Payment();
        $payment->setType('checkmo');
        $payment->setAmount(55.55);
        //
        $order->setPayments([$payment]);
        //
        $req->setSaleOrder($order);
        $resp = $this->obj->orderSave($req);
        $this->assertNotNull($resp);
    }
}