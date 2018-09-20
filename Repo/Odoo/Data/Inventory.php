<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data;

/**
 * Bundle of the products (one product is allowed) to replicate data between Magento and Odoo.
 *
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Inventory
    extends \Praxigento\Core\Data
{
    /**
     * Get array of the lots related to products in the bundle.
     *
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Lot[]
     */
    public function getLots()
    {
        $result = parent::getLots();
        return $result;
    }

    /**
     * Get products bundle options.
     *
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Option
     *
     * @deprecated MOBI-765
     */
    public function getOption()
    {
        $result = parent::getOptions();
        return $result;
    }

    /**
     * Get array of the products to the bundle.
     *
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product[]
     */
    public function getProducts()
    {
        $result = parent::getProducts();
        return $result;
    }

    /**
     * Get array of the warehouses related to products in the bundle.
     *
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Warehouse[]
     */
    public function getWarehouses()
    {
        $result = parent::getWarehouses();
        return $result;
    }

    /**
     * Set array of the lots related to products in the bundle.
     *
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Lot[] $data
     * @return void
     */
    public function setLots($data = null)
    {
        parent::setLots($data);
    }

    /**
     * Set products bundle options.
     *
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Option $data
     * @return void
     *
     * @deprecated MOBI-765
     */
    public function setOption($data = null)
    {
        parent::setOptions($data);
    }

    /**
     * Set array of the products to the bundle.
     *
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product[] $data
     * @return void
     */
    public function setProducts($data = null)
    {
        parent::setProducts($data);
    }

    /**
     * Set array of the warehouses related to products in the bundle.
     *
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Warehouse[] $data
     * @return void
     */
    public function setWarehouses($data = null)
    {
        parent::setWarehouses($data);
    }
}