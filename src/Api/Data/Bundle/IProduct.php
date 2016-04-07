<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle;

/**
 * Data for one product in bundle.
 *
 * @api
 */
interface IProduct
{
    /**
     * Get Odoo ID of the product.
     *
     * @api
     * @return  int|null
     */
    public function getId();

    /**
     * Get wholesale price for the product (see /option/currency to get wholesale currency).
     *
     * @return double
     */
    public function getPrice();

    /**
     * Get wholesale PV for the product.
     *
     * @return double
     */
    public function getPv();

    /**
     * Get SKU for the product.
     *
     * @return string
     */
    public function getSku();

    /**
     * Get array of warehouse data for concrete product in bundle.
     *
     * @return \Praxigento\Odoo\Api\Data\Bundle\Product\IWarehouse[]
     */
    public function getWarehouses();

    /**
     * Set Odoo ID of the product.
     *
     * @api
     * @param int $data
     */
    public function setId($data);

    /**
     * Set wholesale price for the product (see /option/currency to get wholesale currency).
     *
     * @param double $data
     */
    public function setPrice($data);

    /**
     * Set wholesale PV for the product.
     *
     * @param double $data
     */
    public function setPv($data);

    /**
     * Set SKU for the product.
     *
     * @param string $data
     */
    public function setSku($data);

    /**
     * Set array of warehouse data for concrete product in bundle.
     *
     * @param \Praxigento\Odoo\Api\Data\Bundle\Product\IWarehouse[] $data
     */
    public function setWarehouses($data);
}