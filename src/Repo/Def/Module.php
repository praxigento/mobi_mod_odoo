<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Def;

use Praxigento\Odoo\Data\Entity\IOdooEntity;
use Praxigento\Odoo\Data\Entity\Product as EntityProduct;
use Praxigento\Odoo\Repo\IModule;

class Module implements IModule
{
    /** @var \Magento\Catalog\Api\CategoryRepositoryInterface */
    protected $_mageRepoCat;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Praxigento\Core\Repo\IBasic */
    protected $_repoBasic;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Catalog\Api\CategoryRepositoryInterface $repoMageCat,
        \Praxigento\Core\Repo\IBasic $repoBasic
    ) {
        $this->_manObj = $manObj;
        $this->_mageRepoCat = $repoMageCat;
        $this->_repoBasic = $repoBasic;
    }

    /**
     * @inheritdoc
     */
    public function getCategoryIdToPlaceNewProduct()
    {
        $cat = $this->_manObj->create(\Magento\Catalog\Api\Data\CategoryInterface::class);
        $this->_mageRepoCat->get();
//        $this->_repoMageCat->get();
//        $this->_repoMageCat->save();
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function getMageIdByOdooId($entityName, $odooId)
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
     * @inheritdoc
     */
    public function isOdooProductRegisteredInMage($idOdoo)
    {
        $mageId = $this->getMageIdByOdooId(EntityProduct::ENTITY_NAME, $idOdoo);
        $result = !is_null($mageId);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function registerMageIdForOdooId($entityName, $mageId, $odooId)
    {
        $bind = [
            IOdooEntity::ATTR_MAGE_REF => (int)$mageId,
            IOdooEntity::ATTR_ODOO_REF => (int)$odooId
        ];
        $this->_repoBasic->addEntity($entityName, $bind);
    }
}