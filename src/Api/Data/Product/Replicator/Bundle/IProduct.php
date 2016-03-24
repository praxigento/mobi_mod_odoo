<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Product\Replicator\Bundle;

/**
 * Data for one product in bundle.
 */
interface IProduct
{
    /**
     * Wholesale price for the product (see /option/currency to get wholesale currency).
     *
     * @return double|null
     */
    public function getPrice();

    /**
     * Wholesale PV for the product.
     *
     * @return double|null
     */
    public function getPv();

    /**
     * SKU for the product.
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Wholesale price for the product (see /option/currency to get wholesale currency).
     *
     * @param double|null $data
     */
    public function setPrice($data);

    /**
     * Wholesale PV for the product.
     *
     * @param double|null $data
     */
    public function setPv($data);

    /**
     * SKU for the product.
     *
     * @param string|null $data
     */
    public function setSku($data);
}