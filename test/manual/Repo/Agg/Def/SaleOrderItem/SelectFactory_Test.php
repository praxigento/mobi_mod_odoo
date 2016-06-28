<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def\SaleOrderItem;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class SelectFactory_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  SelectFactory */
    private $obj;

    protected function setUp()
    {
        $this->manObj = ObjectManager::getInstance();
        $this->obj = $this->manObj->create(SelectFactory::class);
    }


    public function test_getSelectQuery()
    {
        /** === Test Data === */
        $orderId = 1;
        $stockId = 1;
        /** === Call and asserts  === */
        $res = $this->obj->getSelectQuery();
        /** @var \Magento\Framework\App\ResourceConnection $resr */
        $resr = $this->manObj->get(\Magento\Framework\App\ResourceConnection::class);
        $conn = $resr->getConnection();
        $all = $conn->fetchAll($res,
            [SelectFactory::PARAM_ORDER_ID => $orderId, SelectFactory::PARAM_STOCK_ID => $stockId]);
        $this->assertNotNull($res);
    }


}