<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo;


interface IRegistry
{
    /**
     * Retrieve Mage ID for entity that is replicated with Odoo.
     * @param int $odooId ID in Odoo
     * @return int ID in Magento
     */
    public function getCategoryMageIdByOdooId($odooId);

    /**
     * Retrieve Mage ID for entity that is replicated with Odoo.     *
     * @param int $odooId ID in Odoo
     * @return int ID in Magento
     */
    public function getLotMageIdByOdooId($odooId);

    /**
     * Retrieve Mage ID for entity that is replicated with Odoo.
     * @param int $odooId ID in Odoo
     * @return int ID in Magento
     */
    public function getProductMageIdByOdooId($odooId);

    /**
     * Retrieve Mage ID for entity that is replicated with Odoo.
     * @param int $odooId ID in Odoo
     * @return int ID in Magento
     */
    public function getWarehouseMageIdByOdooId($odooId);

    /**
     * Return 'true' if product with given Odoo ID is already registered in Magento.
     *
     * @param int $odooId
     * @return bool
     */
    public function isProductRegisteredInMage($odooId);

    /**
     * Register relation between Mage & Odoo categories.
     * @param int $mageId
     * @param int $odooId
     */
    public function registerCategory($mageId, $odooId);

    /**
     * Register relation between Mage & Odoo products.
     * @param int $mageId
     * @param int $odooId
     */
    public function registerProduct($mageId, $odooId);
}