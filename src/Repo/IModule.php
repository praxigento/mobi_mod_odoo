<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo;


interface IModule
{
    /**
     * Retrieve ID for the category to place new products into.
     * @return int
     */
    public function getCategoryIdToPlaceNewProduct();

    /**
     * Retrieve Mage ID for entity (product, category, warehouse, lot - see src/Data/Entity) that is
     * replicated with Odoo.
     *
     * @param string $entityName
     * @param int $odooId
     * @return mixed
     */
    public function getMageIdByOdooId($entityName, $odooId);

    /**
     * Return 'true' if product with given Odoo ID is already registered in Magento.
     *
     * @param int $idOdoo
     * @return bool
     */
    public function isOdooProductRegisteredInMage($idOdoo);

    /**
     * Register relation between Mage & Odoo instances of the $entityName.
     * @param string $entityName
     * @param int $mageId
     * @param int $odooId
     */
    public function registerMageIdForOdooId($entityName, $mageId, $odooId);
}