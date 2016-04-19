<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity;
use Praxigento\Odoo\Repo\Agg\IWarehouse;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Warehouse_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  IWarehouse */
    private $obj;

    protected function setUp()
    {
        $this->manObj = ObjectManager::getInstance();
        $this->obj = $this->manObj->create(IWarehouse::class);
    }


    public function test_getById()
    {
        /* === Test Data === */
        $id = 1;
        /* === Call and asserts  === */
        $res = $this->obj->getById($id);
        $this->assertNotNull($res);
    }


}