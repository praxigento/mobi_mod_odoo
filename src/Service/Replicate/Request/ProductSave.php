<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Request;

use Praxigento\Odoo\Data\Odoo\Inventory;

class ProductSave extends \Praxigento\Core\Service\Base\Request
{
    /**
     * Get bundle of the products (one product is also allowed) to replicate data between Magento and Odoo.
     * @return  \Praxigento\Odoo\Data\Odoo\Inventory
     */
    public function getProductBundle()
    {
        $data = parent::getProductBundle();
        $result = new \Praxigento\Odoo\Data\Odoo\Inventory($data);
        return $result;
    }

    /**
     * Get bundle of the products (one product is also allowed) to replicate data between Magento and Odoo.
     *
     * @param \Praxigento\Odoo\Data\Odoo\Inventory $data
     */
    public function setProductBundle(\Praxigento\Odoo\Data\Odoo\Inventory $data)
    {
        parent::setProductBundle($data);
    }
}