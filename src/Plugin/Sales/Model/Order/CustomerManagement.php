<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Plugin\Sales\Model\Order;


class CustomerManagement
{
    /** @var  \Praxigento\Odoo\Service\IReplicate */
    protected $callReplicate;
    /** @var \Magento\Sales\Api\OrderRepositoryInterface */
    protected $repoOrder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $repoOrder,
        \Praxigento\Odoo\Service\IReplicate $callReplicate
    ) {
        $this->repoOrder = $repoOrder;
        $this->callReplicate = $callReplicate;
    }

    public function aroundCreate(
        \Magento\Sales\Model\Order\CustomerManagement $subject,
        \Closure $proceed,
        $orderId
    ) {
        $result = $proceed($orderId);
        $req = new \Praxigento\Odoo\Service\Replicate\Request\OrderSave();
        $order = $this->repoOrder->get($orderId);
        $req->setSaleOrder($order);
        $this->callReplicate->orderSave($req);
        return $result;
    }
}