<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Def;

use Praxigento\Odoo\Repo\IPvModule;
use Praxigento\Pv\Data\Entity\Product as EntityPvProduct;
use Praxigento\Pv\Data\Entity\Stock\Item as EntityPvStockItem;

class PvModule implements IPvModule
{
    /** @var \Praxigento\Core\Repo\IBasic */
    protected $_repoBasic;

    public function __construct(
        \Praxigento\Core\Repo\IBasic $repoBasic
    ) {
        $this->_repoBasic = $repoBasic;
    }

    public function saveProductWholesalePv($productMageId, $pv)
    {
        $bind = [
            EntityPvProduct::ATTR_PROD_REF => $productMageId,
            EntityPvProduct::ATTR_PV => $pv
        ];
        $this->_repoBasic->addEntity(EntityPvProduct::ENTITY_NAME, $bind);
    }

    public function saveWarehousePv($stockItemMageId, $pv)
    {
        $bind = [
            EntityPvStockItem::ATTR_STOCK_ITEM_REF => $stockItemMageId,
            EntityPvStockItem::ATTR_PV => $pv
        ];
        $this->_repoBasic->addEntity(EntityPvStockItem::ENTITY_NAME, $bind);
    }

    public function updateProductWholesalePv($productMageId, $pv)
    {
        $bind = [
            EntityPvProduct::ATTR_PROD_REF => $productMageId,
            EntityPvProduct::ATTR_PV => $pv
        ];
        $where = EntityPvProduct::ATTR_PROD_REF . '=' . (int)$productMageId;
        $this->_repoBasic->updateEntity(EntityPvProduct::ENTITY_NAME, $bind, $where);
    }

    public function updateWarehousePv($stockItemMageId, $pv)
    {
        $bind = [
            EntityPvStockItem::ATTR_STOCK_ITEM_REF => $stockItemMageId,
            EntityPvStockItem::ATTR_PV => $pv
        ];
        $where = EntityPvStockItem::ATTR_STOCK_ITEM_REF . '=' . (int)$stockItemMageId;
        $this->_repoBasic->updateEntity(EntityPvStockItem::ENTITY_NAME, $bind, $where);
    }
}