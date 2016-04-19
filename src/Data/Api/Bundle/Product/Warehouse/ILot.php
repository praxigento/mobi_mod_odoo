<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Api\Bundle\Product\Warehouse;


/**
 * Lot data for the product on the warehouse.
 *
 * @api
 */
interface ILot
{
    /**
     * Get Odoo ID of the lot.
     *
     * @return  int|null
     */
    public function getId();

    /**
     * Get quantity of the product on the warehouse.
     *
     * @return double
     */
    public function getQuantity();

    /**
     * Set Odoo ID of the lot.
     *
     * @param int $data
     */
    public function setId($data);

    /**
     * Set quantity of the product on the warehouse.
     *
     * @param double $data
     */
    public function setQuantity($data);
}