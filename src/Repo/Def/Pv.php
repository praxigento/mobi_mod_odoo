<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Def;

use Praxigento\Odoo\Repo\IPv;
use Praxigento\Pv\Data\Entity\Product as EntityPvProduct;
use Praxigento\Pv\Data\Entity\Stock\Item as EntityPvStockItem;

class Pv implements IPv
{
    /** @var  \Praxigento\Pv\Repo\Entity\IProduct */
    protected $_repoPvProduct;
    /** @var \Praxigento\Pv\Repo\Entity\Stock\IItem */
    protected $_repoPvStockItem;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\IProduct $repoPvProduct,
        \Praxigento\Pv\Repo\Entity\Stock\IItem $repoPvStockItem
    ) {
        $this->_repoPvProduct = $repoPvProduct;
        $this->_repoPvStockItem = $repoPvStockItem;
    }

    public function getWarehousePv($stockItemMageRef)
    {
        $result = null;
        $data = $this->_repoPvStockItem->getById($stockItemMageRef);
        if (isset($data[EntityPvStockItem::ATTR_PV])) {
            $result = $data[EntityPvStockItem::ATTR_PV];
        }
        return $result;
    }

    public function registerProductWholesalePv($productMageId, $pv)
    {
        $bind = [
            EntityPvProduct::ATTR_PROD_REF => $productMageId,
            EntityPvProduct::ATTR_PV => $pv
        ];
        $this->_repoPvProduct->create($bind);
    }

    public function registerWarehousePv($stockItemMageRef, $pv)
    {
        $bind = [
            EntityPvStockItem::ATTR_STOCK_ITEM_REF => $stockItemMageRef,
            EntityPvStockItem::ATTR_PV => $pv
        ];
        $this->_repoPvStockItem->create($bind);
    }

    public function updateProductWholesalePv($productMageRef, $pv)
    {
        $bind = [
            EntityPvProduct::ATTR_PROD_REF => $productMageRef,
            EntityPvProduct::ATTR_PV => $pv
        ];
        $where = EntityPvProduct::ATTR_PROD_REF . '=' . (int)$productMageRef;
        $this->_repoPvProduct->update($bind, $where);
    }

    public function updateWarehousePv($stockItemMageRef, $pv)
    {
        $bind = [
            EntityPvStockItem::ATTR_STOCK_ITEM_REF => $stockItemMageRef,
            EntityPvStockItem::ATTR_PV => $pv
        ];
        $where = EntityPvStockItem::ATTR_STOCK_ITEM_REF . '=' . (int)$stockItemMageRef;
        $this->_repoPvStockItem->update($bind, $where);
    }
}