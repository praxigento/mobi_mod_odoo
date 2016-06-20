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
        $req->setSaleOrder($order);
        $resp = $this->obj->orderSave($req);
        $this->assertNotNull($resp);
    }
}