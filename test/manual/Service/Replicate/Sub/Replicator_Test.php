<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Api\Data\Bundle\ICategory as ApiCategory;
use Praxigento\Odoo\Service\Replicate\Sub\Replicator;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Replicator_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{
    /** @var  ObjectManagerInterface */
    private $manObj;
    /** @var  Replicator */
    private $obj;

    protected function setUp()
    {
        $this->_manObj = \Magento\Framework\App\ObjectManager::getInstance();
        $this->obj = $this->manObj->create(Replicator::class);
    }

    public function test_processCategories()
    {
        /* === Test Data === */
        $cats = [];
        /** @var  $cat ApiCategory */
        $cat = $this->manObj->create(ApiCategory::class);
        $cat->setId(32);
        $cat->setName("Odoo category");
        /* === Call and asserts  === */
        $this->obj->processCategories($cats);
    }

}