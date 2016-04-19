<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Api;

/**
 * Bundle of the products (one product is allowed) to replicate data between Magento and Odoo.
 *
 * @api
 */
interface IBundle
{

    /**
     * Get array of the lots related to products in the bundle.
     *
     * @return \Praxigento\Odoo\Data\Api\Bundle\ILot[]
     */
    public function getLots();

    /**
     * Get products bundle options.
     *
     * @return \Praxigento\Odoo\Data\Api\Bundle\IOption
     */
    public function getOption();

    /**
     * Get array of the products to the bundle.
     *
     * @return \Praxigento\Odoo\Data\Api\Bundle\IProduct[]
     */
    public function getProducts();

    /**
     * Get array of the warehouses related to products in the bundle.
     *
     * @return \Praxigento\Odoo\Data\Api\Bundle\IWarehouse[]
     */
    public function getWarehouses();

    /**
     * Set array of the lots related to products in the bundle.
     *
     * @param \Praxigento\Odoo\Data\Api\Bundle\ILot[] $data
     */
    public function setLots($data);

    /**
     * Set products bundle options.
     *
     * @param \Praxigento\Odoo\Data\Api\Bundle\IOption $data
     */
    public function setOption($data);

    /**
     * Set array of the products to the bundle.
     *
     * @param \Praxigento\Odoo\Data\Api\Bundle\IProduct[] $data
     */
    public function setProducts($data);

    /**
     * Set array of the warehouses related to products in the bundle.
     *
     * @param \Praxigento\Odoo\Data\Api\Bundle\IWarehouse[] $data
     */
    public function setWarehouses($data);
}