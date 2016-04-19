<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Api\Bundle;

/**
 * Data for one product in bundle.
 *
 * @api
 */
interface IProduct
{
    /**
     * Get array of the categories ids where this product is placed.
     *
     * @return int[]
     */
    public function getCategories();

    /**
     * Get Odoo ID of the product.
     *
     * @return  int|null
     */
    public function getId();

    /**
     * Get activity status.
     *
     * @return  bool
     */
    public function getIsActive();

    /**
     * Get name for the product.
     *
     * @return string
     */
    public function getName();

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
     * @return \Praxigento\Odoo\Data\Api\Bundle\Product\IWarehouse[]
     */
    public function getWarehouses();

    /**
     * Get weight for the product.
     *
     * @return double
     */
    public function getWeight();

    /**
     * Set array of the categories ids where this product is placed.
     *
     * @param int[] $data
     */
    public function setCategories($data);

    /**
     * Set Odoo ID of the product.
     *
     * @param int $data
     */
    public function setId($data);

    /**
     * Set activity status.
     *
     * @param bool $data
     */
    public function setIsActive($data);

    /**
     * Set name for the product.
     *
     * @param string $data
     */
    public function setName($data);

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
     * @param \Praxigento\Odoo\Data\Api\Bundle\Product\IWarehouse[] $data
     */
    public function setWarehouses($data);

    /**
     * Set weight for the product.
     *
     * @param double $data
     */
    public function setWeight($data);
}