<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle\Product\Warehouse;


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
     * @api
     * @return  int|null
     */
    public function getId();

    /**
     * Get quantity of the product on the warehouse.
     *
     * @return double
     */
    public function getQty();

    /**
     * Set Odoo ID of the lot.
     *
     * @api
     * @param int $data
     */
    public function setId($data);

    /**
     * Set quantity of the product on the warehouse.
     *
     * @param double $data
     */
    public function setQty($data);
}