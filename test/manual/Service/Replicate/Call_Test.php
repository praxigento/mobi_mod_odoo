<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate;

use Magento\Framework\App\ObjectManager;
use Praxigento\Odoo\Service\IReplicate;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  IReplicate */
    private $obj;
    /** @var  \Magento\Sales\Api\OrderRepositoryInterface */
    private $_mageRepoSaleOrder;

    protected function setUp()
    {
        $this->_mageRepoSaleOrder = $this->_manObj->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $this->obj = $this->_manObj->create(IReplicate::class);
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

        /* load Magento order */
        $mageOrder = $this->_mageRepoSaleOrder->get(1);
        $req->setSaleOrder($mageOrder);
        $resp = $this->obj->orderSave($req);
        $this->assertNotNull($resp);
    }
}