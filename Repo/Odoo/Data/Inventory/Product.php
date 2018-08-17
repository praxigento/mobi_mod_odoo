<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\Inventory;

/**
 * Data for one product in replication bundle.
 *
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 *
 */
class Product
    extends \Praxigento\Core\Data
{
    /**
     * Get array of the categories ids where this product is placed.
     *
     * @return int[]
     */
    public function getCategories()
    {
        $result = parent::getCategories();
        return $result;
    }

    /**
     * Get Odoo ID of the product.
     *
     * @return  int|null
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
        return $result;
    }

    /**
     * Get activity status.
     *
     * @return  bool
     */
    public function getIsActive()
    {
        $result = parent::getIsActive();
        return $result;
    }

    /**
     * Get name for the product.
     *
     * @return string
     */
    public function getName()
    {
        $result = parent::getName();
        return $result;
    }

    /**
     * Get retail price for the product (see /option/currency for the currency).
     *
     * @return float
     *
     * @deprecated MOBI-765
     */
    public function getPriceRetail()
    {
        $result = parent::getPriceRetail();
        return $result;
    }

    /**
     * Get wholesale price for the product (see /option/currency to get wholesale currency).
     *
     * @return float
     *
     * @deprecated MOBI-765
     */
    public function getPriceWholesale()
    {
        $result = parent::getPriceWholesale();
        return $result;
    }

    /**
     * Get wholesale PV for the product.
     *
     * @return float
     */
    public function getPvWholesale()
    {
        $result = parent::getPvWholesale();
        return $result;
    }

    /**
     * Get SKU for the product.
     *
     * @return string
     */
    public function getSku()
    {
        $result = parent::getSku();
        return $result;
    }

    /**
     * Get array of warehouse data for concrete product in bundle.
     *
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse[]
     */
    public function getWarehouses()
    {
        $result = parent::getWarehouses();
        return $result;
    }

    /**
     * Get weight for the product.
     *
     * @return float
     */
    public function getWeight()
    {
        $result = parent::getWeight();
        return $result;
    }

    /**
     * Set array of the categories ids where this product is placed.
     *
     * @param int[] $data
     */
    public function setCategories($data)
    {
        parent::setCategories($data);
    }

    /**
     * Set Odoo ID of the product.
     *
     * @param int $data
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }

    /**
     * Set activity status.
     *
     * @param bool $data
     */
    public function setIsActive($data)
    {
        parent::setIsActive($data);
    }

    /**
     * Set name for the product.
     *
     * @param string $data
     */
    public function setName($data)
    {
        parent::setName($data);
    }

    /**
     * Set куефшд price for the product (see /option/currency for the currency).
     *
     * @param float $data
     *
     * @deprecated MOBI-765
     */
    public function setPriceRetail($data)
    {
        parent::setPriceWholesale($data);
    }

    /**
     * Set wholesale price for the product (see /option/currency to get wholesale currency).
     *
     * @param float $data
     *
     * @deprecated MOBI-765
     */
    public function setPriceWholesale($data)
    {
        parent::setPriceWholesale($data);
    }

    /**
     * Set wholesale PV for the product.
     *
     * @param float $data
     */
    public function setPvWholesale($data)
    {
        parent::setPvWholesale($data);
    }

    /**
     * Set SKU for the product.
     *
     * @param string $data
     */
    public function setSku($data)
    {
        parent::setSku($data);
    }

    /**
     * Set array of warehouse data for concrete product in bundle.
     *
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse[] $data
     */
    public function setWarehouses($data)
    {
        parent::setWarehouses($data);
    }

    /**
     * Set weight for the product.
     *
     * @param float $data
     */
    public function setWeight($data)
    {
        parent::setWeight($data);
    }
}