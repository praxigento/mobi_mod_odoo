<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity;

use Magento\Framework\App\ObjectManager;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class SaleOrder_ManualTest
    extends \Praxigento\Core\Test\BaseCase\Mockery
{
    /** @var  \Praxigento\Odoo\Api\Def\SaleOrderReplicator\Collector */
    private $_api;
    /** @var  \Praxigento\Odoo\Repo\Entity\SaleOrder */
    private $_obj;

    public function setUp()
    {
        parent::setUp();
        $this->_obj = ObjectManager::getInstance()->create(\Praxigento\Odoo\Repo\Entity\SaleOrder::class);
        $this->_api = ObjectManager::getInstance()->create(\Praxigento\Odoo\Api\Def\SaleOrderReplicator\Collector::class);
    }

    public function test_getIdsToSaveToOdoo()
    {
//        $res = $this->_obj->getIdsToSaveToOdoo();
//        $this->assertTrue($res > 0);
        $res = $this->_api->getOrdersToReplicate();
    }


}