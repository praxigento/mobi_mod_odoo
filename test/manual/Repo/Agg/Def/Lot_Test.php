<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity;
use Praxigento\Odoo\Repo\Agg\ILot;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Lot_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  Lot */
    private $obj;

    protected function setUp()
    {
        $this->manObj = ObjectManager::getInstance();
        $this->obj = $this->manObj->create(ILot::class);
    }


    public function test_getById()
    {
        /* === Test Data === */
        $id = 1;
        /* === Call and asserts  === */
        $res = $this->obj->getById($id);
        $this->assertNotNull($res);
    }
    public function test_getByOdooId()
    {
        /* === Test Data === */
        $id = 1;
        /* === Call and asserts  === */
        $res = $this->obj->getByOdooId($id);
        $this->assertNotNull($res);
    }


}