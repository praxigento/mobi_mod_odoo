<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse;

/**
 * Lot data for the product on the warehouse.
 */
class Lot
    extends \Flancer32\Lib\Data
{
    /**
     * Get Odoo ID of the lot.
     *
     * @return  int|null
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
        return $result;
    }

    /**
     * Get quantity of the product on the warehouse.
     *
     * @return double
     */
    public function getQuantity()
    {
        $result = parent::getQuantity();
        return $result;
    }

    /**
     * Set Odoo ID of the lot.
     *
     * @param int $data
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }

    /**
     * Set quantity of the product on the warehouse.
     *
     * @param double $data
     */
    public function setQuantity($data)
    {
        parent::setQuantity($data);
    }
}