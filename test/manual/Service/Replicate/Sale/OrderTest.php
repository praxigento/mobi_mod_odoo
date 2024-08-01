<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Praxigento\Odoo\Service\Replicate\Sale\Order;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class OrderTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\Order */
    private $obj;

    protected function setUp(): void
    {
        $this->obj = $this->manObj->create(\Praxigento\Odoo\Service\Replicate\Sale\Order::class);
    }


    public function test_exec()
    {
        /** @var \Magento\Sales\Api\OrderRepositoryInterface $repo */
        $repo = $this->manObj->create(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $sale = $repo->get(10619);
        $req = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Request();
        $req->setSaleOrder($sale);
        $res = $this->obj->exec($req);
        $this->assertNotNull($res);
    }


}
