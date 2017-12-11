<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Request;

class OrderSave extends \Praxigento\Core\App\Service\Base\Request
{
    /**
     * @return \Magento\Sales\Api\Data\OrderInterface
     */
    public function getSaleOrder()
    {
        $result = parent::getSaleOrder();
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $data
     */
    public function setSaleOrder($data = null)
    {
        parent::setSaleOrder($data);
    }
}