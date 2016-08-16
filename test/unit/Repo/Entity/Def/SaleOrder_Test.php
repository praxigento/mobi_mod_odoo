<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Entity\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class SaleOrder_UnitTest extends \Praxigento\Core\Test\BaseRepoEntityCase
{
    /** @var  SaleOrder */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create object to test */
        $this->obj = new SaleOrder(
            $this->mResource,
            $this->mRepoGeneric
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(SaleOrder::class, $this->obj);
    }

}