<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo;

/**
 * @deprecated use Aggregates & Entites repos instead.
 */
interface IRegistry
{
    /**
     * Retrieve Mage ID for entity that is replicated with Odoo.
     * @param int $odooId ID in Odoo
     * @return int ID in Magento
     * @deprecated use Aggregates & Entites repos instead.
     */
    public function getCategoryMageIdByOdooId($odooId);

    /**
     * Retrieve Mage ID for entity that is replicated with Odoo.     *
     * @param int $odooId ID in Odoo
     * @return int ID in Magento
     * @deprecated use Aggregates & Entites repos instead.
     */
    public function getLotMageIdByOdooId($odooId);

    /**
     * Retrieve Mage ID for entity that is replicated with Odoo.
     * @param int $odooId ID in Odoo
     * @return int ID in Magento
     * @deprecated use Aggregates & Entites repos instead.
     */
    public function getProductMageIdByOdooId($odooId);

    /**
     * Retrieve Mage ID for entity that is replicated with Odoo.
     * @param int $odooId ID in Odoo
     * @return int ID in Magento
     * @deprecated use Aggregates & Entites repos instead.
     */
    public function getWarehouseMageIdByOdooId($odooId);

    /**
     * Return 'true' if product with given Odoo ID is already registered in Magento.
     *
     * @param int $odooId
     * @return bool
     * @deprecated use Aggregates & Entites repos instead.
     */
    public function isProductRegisteredInMage($odooId);

    /**
     * Register relation between Mage & Odoo categories.
     * @param int $mageId
     * @param int $odooId
     * @deprecated use Aggregates & Entites repos instead.
     */
    public function registerCategory($mageId, $odooId);

    /**
     * Register relation between Mage & Odoo products.
     * @param int $mageId
     * @param int $odooId
     * @deprecated use Aggregates & Entites repos instead.
     */
    public function registerProduct($mageId, $odooId);
}