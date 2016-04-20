<?php
/**
 * Repository to register relations between instances (products, categories, etc) in Odoo & Magento.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Def;

use Praxigento\Odoo\Data\Entity\Category as EntityCategory;
use Praxigento\Odoo\Data\Entity\IOdooEntity;
use Praxigento\Odoo\Data\Entity\Lot as EntityLot;
use Praxigento\Odoo\Data\Entity\Product as EntityProduct;
use Praxigento\Odoo\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Odoo\Repo\IRegistry;

class Registry implements IRegistry
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Praxigento\Core\Repo\IGeneric */
    protected $_repoBasic;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\IGeneric $repoBasic
    ) {
        $this->_manObj = $manObj;
        $this->_repoBasic = $repoBasic;
    }

    /**
     * Retrieve Mage ID for entity that is replicated with Odoo.
     *
     * @param string $entityName
     * @param int $odooId
     * @return int
     */
    protected function _getMageIdByOdooId($entityName, $odooId)
    {
        $result = null;
        $where = IOdooEntity::ATTR_ODOO_REF . '=' . (int)$odooId;
        $items = $this->_repoBasic->getEntities($entityName, null, $where);
        if (
            is_array($items) &&
            (count($items) == 1)
        ) {
            $item = reset($items);
            $result = $item[IOdooEntity::ATTR_MAGE_REF];
        }
        return $result;
    }

    /**
     * Registry new relation between instances in Odoo & Magento.
     * @param string $entityName
     * @param int $mageId
     * @param int $odooId
     */
    protected function _registerMageIdForOdooId($entityName, $mageId, $odooId)
    {
        $bind = [
            IOdooEntity::ATTR_MAGE_REF => (int)$mageId,
            IOdooEntity::ATTR_ODOO_REF => (int)$odooId
        ];
        $this->_repoBasic->addEntity($entityName, $bind);
    }

    /**
     * @inheritdoc
     */
    public function getCategoryMageIdByOdooId($odooId)
    {
        $result = $this->_getMageIdByOdooId(EntityCategory::ENTITY_NAME, $odooId);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getLotMageIdByOdooId($odooId)
    {
        $result = $this->_getMageIdByOdooId(EntityLot::ENTITY_NAME, $odooId);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getProductMageIdByOdooId($odooId)
    {
        $result = $this->_getMageIdByOdooId(EntityProduct::ENTITY_NAME, $odooId);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getWarehouseMageIdByOdooId($odooId)
    {
        $result = $this->_getMageIdByOdooId(EntityWarehouse::ENTITY_NAME, $odooId);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function isProductRegisteredInMage($odooId)
    {
        $mageId = $this->getProductMageIdByOdooId($odooId);
        $result = !is_null($mageId);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function registerCategory($mageId, $odooId)
    {
        $this->_registerMageIdForOdooId(EntityCategory::ENTITY_NAME, $mageId, $odooId);
    }

    /**
     * @inheritdoc
     */
    public function registerProduct($mageId, $odooId)
    {
        $this->_registerMageIdForOdooId(EntityProduct::ENTITY_NAME, $mageId, $odooId);
    }
}