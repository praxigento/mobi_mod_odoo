<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save;

class Request
    extends \Praxigento\Core\Data
{
    const INVENTORY = 'inventory';

    /**
     * Get bundle of the products to replicate data between Magento and Odoo.
     * @return  \Praxigento\Odoo\Repo\Odoo\Data\Inventory
     */
    public function getInventory()
    {
        $result = parent::get(self::INVENTORY);
        return $result;
    }

    /**
     * Set bundle of the products to replicate data between Magento and Odoo.
     *
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory $data
     */
    public function setInventory(\Praxigento\Odoo\Repo\Odoo\Data\Inventory $data)
    {
        parent::set(self::INVENTORY, $data);
    }
}