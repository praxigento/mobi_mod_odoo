<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product;

/**
 * Warehouse data for concrete product in bundle.
 *
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 *
 */
class Warehouse
    extends \Praxigento\Core\Data
{
    /**
     * Get Odoo ID of the warehouse.
     *
     * @return  int|null
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
        return $result;
    }

    /**
     * Get array of lots data for the product on the warehouse.
     *
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\Lot[]
     */
    public function getLots()
    {
        $result = parent::getLots();
        return $result;
    }

    /**
     * Get price for the product on the concrete warehouse.
     *
     * @return double
     */
    public function getPriceWarehouse()
    {
        $result = parent::getPriceWarehouse();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\GroupPrice\Item[]
     */
    public function getPrices()
    {
        $result = parent::getPrices();
        return $result;
    }

    /**
     * Get PV for the product on the concrete warehouse.
     *
     * @return double
     */
    public function getPvWarehouse()
    {
        $result = parent::getPvWarehouse();
        return $result;
    }

    /**
     * Set Odoo ID of the warehouse.
     *
     * @param int $data
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }

    /**
     * Set array of lots data for the product on the warehouse.
     *
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\Lot[] $data
     */
    public function setLots($data)
    {
        parent::setLots($data);
    }

    /**
     * Set price for the product on the concrete warehouse.
     *
     * @param double $data
     */
    public function setPriceWarehouse($data)
    {
        parent::setPriceWarehouse($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\GroupPrice\Item[] $data
     */
    public function setPrices($data)
    {
        parent::setPrices($data);
    }

    /**
     * Set PV for the product on the concrete warehouse.
     *
     * @param double $data
     */
    public function setPvWarehouse($data)
    {
        parent::setPvWarehouse($data);
    }
}