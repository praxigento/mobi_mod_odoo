<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Def;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity;
use Praxigento\Odoo\Repo\IModule;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Module_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  IModule */
    private $obj;

    protected function setUp()
    {
        $this->manObj = ObjectManager::getInstance();
        $this->obj = $this->manObj->create(IModule::class);
    }

    public function test_getMageIdByOdooId()
    {
        /* === Test Data === */
        $idOdoo = 43;
        /* === Call and asserts  === */
        $res = $this->obj->getMageIdByOdooId(Entity\Category::ENTITY_NAME, $idOdoo);
        $this->assertNotNull($res);
    }

    public function test_isOdooProductRegisteredInMage()
    {
        /* === Test Data === */
        $idOdoo = 43;
        /* === Call and asserts  === */
        $res = $this->obj->isOdooProductRegisteredInMage($idOdoo);
        $this->assertTrue($res);
    }


}