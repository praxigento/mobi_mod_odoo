<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Request;

class OrderSave extends \Praxigento\Core\Service\Base\Request
{
    /**
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder
     */
    public function getSaleOrder()
    {
        $result = parent::getSaleOrder();
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Data\Odoo\SaleOrder $data
     */
    public function setSaleOrder($data = null)
    {
        parent::setSaleOrder($data);
    }
}