<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity;
use Praxigento\Odoo\Repo\Agg\ISaleOrderItem;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class SaleOrderItem_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  SaleOrderItem */
    private $obj;

    protected function setUp()
    {
        $this->manObj = ObjectManager::getInstance();
        $this->obj = $this->manObj->create(ISaleOrderItem::class);
    }


    public function test_getByOrderAndStock()
    {
        /** === Test Data === */
        $orderId = 1;
        $stockId = 1;
        /** === Call and asserts  === */
        $res = $this->obj->getByOrderAndStock($orderId, $stockId);
        $this->assertNotNull($res);
    }


}