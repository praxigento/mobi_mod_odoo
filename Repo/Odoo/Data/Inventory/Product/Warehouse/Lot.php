<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse;

/**
 * Lot data for the product on the warehouse.
 */
class Lot
    extends \Praxigento\Core\Data
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
     * @return float
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
     * @return void
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }

    /**
     * Set quantity of the product on the warehouse.
     *
     * @param float $data
     * @return void
     */
    public function setQuantity($data)
    {
        parent::setQuantity($data);
    }
}