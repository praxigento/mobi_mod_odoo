<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle\Product;


/**
 * Warehouse data for concrete product in bundle.
 *
 * @api
 */
interface IWarehouse
{
    /**
     * Get Odoo ID of the warehouse.
     *
     * @api
     * @return  int|null
     */
    public function getId();

    /**
     * Get array of lots data for the product on the warehouse.
     *
     * @return \Praxigento\Odoo\Api\Data\Bundle\Product\Warehouse\ILot[]
     */
    public function getLots();

    /**
     * Get price for the product on the concrete warehouse.
     *
     * @return double
     */
    public function getPrice();

    /**
     * Get PV for the product on the concrete warehouse.
     *
     * @return double
     */
    public function getPv();

    /**
     * Set Odoo ID of the warehouse.
     *
     * @api
     * @param int $data
     */
    public function setId($data);

    /**
     * Set array of lots data for the product on the warehouse.
     *
     * @param \Praxigento\Odoo\Api\Data\Bundle\Product\Warehouse\ILot[] $data
     */
    public function setLots($data);

    /**
     * Set price for the product on the concrete warehouse.
     *
     * @param double $data
     */
    public function setPrice($data);

    /**
     * Set PV for the product on the concrete warehouse.
     *
     * @param double $data
     */
    public function setPv($data);
}