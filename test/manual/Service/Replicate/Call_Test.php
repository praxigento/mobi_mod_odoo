<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Service\IReplicate;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class Call_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
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

    public function test_rep()
    {
        $req = new Request\ProductsFromOdoo();
        $resp = $this->obj->productsFromOdoo($req);
        $this->assertNotNull($resp);
    }
}