<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\GroupPrice;

/**
 * Customer group price for product in the warehouse.
 *
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Item
    extends \Praxigento\Core\Data
{
    /**
     * Business code for customer group.
     *
     * @return string
     */
    public function getGroupCode()
    {
        $result = parent::getGroupCode();
        return $result;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        $result = parent::getPrice();
        return $result;
    }

    /**
     * Business code for customer group.
     *
     * @param string $data
     * @return void
     */
    public function setGroupCode($data)
    {
        parent::setGroupCode($data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setPrice($data)
    {
        parent::setPrice($data);
    }
}