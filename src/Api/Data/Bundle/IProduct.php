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
     * Wholesale price for the product (see /option/currency to get wholesale currency).
     *
     * @api
     * @return double
     */
    public function getPrice();

    /**
     * Wholesale PV for the product.
     *
     * @api
     * @return double
     */
    public function getPv();

    /**
     * SKU for the product.
     *
     * @api
     * @return string
     */
    public function getSku();

    /**
     * Wholesale price for the product (see /option/currency to get wholesale currency).
     *
     * @api
     * @param double $data
     */
    public function setPrice($data);

    /**
     * Wholesale PV for the product.
     *
     * @api
     * @param double $data
     */
    public function setPv($data);

    /**
     * SKU for the product.
     *
     * @api
     * @param string $data
     */
    public function setSku($data);
}