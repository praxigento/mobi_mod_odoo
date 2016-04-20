<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Def;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity;
use Praxigento\Odoo\Repo\IRegistry;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Module_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  IRegistry */
    private $obj;

    protected function setUp()
    {
        $this->manObj = ObjectManager::getInstance();
        $this->obj = $this->manObj->create(IRegistry::class);
    }


    public function test_isOdooProductRegisteredInMage()
    {
        /* === Test Data === */
        $idOdoo = 43;
        /* === Call and asserts  === */
        $res = $this->obj->isProductRegisteredInMage($idOdoo);
        $this->assertTrue($res);
    }


}