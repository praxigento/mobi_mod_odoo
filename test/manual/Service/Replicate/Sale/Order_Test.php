<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Order_Test
    extends \Praxigento\Core\Test\BaseCase\Manual
{
    protected function getOrder()
    {
        /** @var \Magento\Sales\Api\OrderRepositoryInterface $repo */
        $repo = $this->manObj->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $result = $repo->get(20);
        return $result;
    }

    public function test_get()
    {
        /** @var \Praxigento\Odoo\Service\Replicate\Sale\Order $obj */
        $obj = $this->manObj->create(\Praxigento\Odoo\Service\Replicate\Sale\IOrder::class);
        $req = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Request();
        $order = $this->getOrder();
        $req->setSaleOrder($order);
        $res = $obj->exec($req);
        $this->assertNotNull($res);
    }
}