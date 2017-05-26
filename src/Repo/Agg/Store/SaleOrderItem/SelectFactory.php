<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Store\SaleOrderItem;

use Magento\Sales\Api\Data\OrderItemInterface as MageEntityOrderItem;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Entity\Lot as EntityOdooLot;
use Praxigento\Odoo\Data\Entity\Product as EntityOdooProduct;
use Praxigento\Odoo\Repo\Agg\Data\SaleOrderItem as Agg;
use Praxigento\Pv\Data\Entity\Sale\Item as EntityPvSaleItem;
use Praxigento\Pv\Data\Entity\Stock\Item as EntityPvStockItem;
use Praxigento\Warehouse\Data\Entity\Quantity\Sale as EntityWrhsQtySale;

/**
 * Compose SELECT query to get Sale Order Item aggregate.
 *
 * @deprecated use \Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get\Builder
 */
class SelectFactory
    extends \Praxigento\Core\Repo\Agg\BaseSelectFactory
{
    /**#@+
     * Query parameters names.
     */
    const PARAM_ORDER_ID = 'order_id';
    const PARAM_STOCK_ID = 'stock_id';

    /**#@- */

    public function getQueryToSelect()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asSaleItem = 'saleItem';
        $asStockItem = 'stockItem';
        $asPvSaleItem = 'pvSaleItem';
        $asPvStockItem = 'pvStockItem';
        $asQtySale = 'wrhsQtySale';
        $asOdooProd = 'odooProd';
        $asOdooLot = 'odooLot';
        $tblSaleItem = [$asSaleItem => $this->_resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM)];
        $tblStockItem = [$asStockItem => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM)];
        $tblPvSaleItem = [$asPvSaleItem => $this->_resource->getTableName(EntityPvSaleItem::ENTITY_NAME)];
        $tblPvStockItem = [$asPvStockItem => $this->_resource->getTableName(EntityPvStockItem::ENTITY_NAME)];
        $tblQtySale = [$asQtySale => $this->_resource->getTableName(EntityWrhsQtySale::ENTITY_NAME)];
        $tblOdooProd = [$asOdooProd => $this->_resource->getTableName(EntityOdooProduct::ENTITY_NAME)];
        $tblOdooLot = [$asOdooLot => $this->_resource->getTableName(EntityOdooLot::ENTITY_NAME)];
        /* FROM sales_order_item */
        $cols = [
            Agg::AS_ITEM_QTY => MageEntityOrderItem::QTY_ORDERED,
            Agg::AS_PRICE_DISCOUNT => MageEntityOrderItem::BASE_DISCOUNT_AMOUNT,
            Agg::AS_PRICE_TAX_PERCENT => MageEntityOrderItem::TAX_PERCENT,
            Agg::AS_PRICE_TOTAL => MageEntityOrderItem::BASE_ROW_TOTAL,
            Agg::AS_PRICE_TOTAL_WITH_TAX => MageEntityOrderItem::BASE_ROW_TOTAL_INCL_TAX,
            Agg::AS_PRICE_UNIT_ORIG => MageEntityOrderItem::BASE_ORIGINAL_PRICE,
            Agg::AS_PRICE_UNIT => MageEntityOrderItem::BASE_PRICE_INCL_TAX,
        ];
        $result->from($tblSaleItem, $cols);
        /* LEFT JOIN cataloginventory_stock_item */
        $cols = [];
        $cond = $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . $asSaleItem . '.' . MageEntityOrderItem::PRODUCT_ID;
        $cond .= ' AND ' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:' . self::PARAM_STOCK_ID;
        $result->joinLeft($tblStockItem, $cond, $cols);
        /* LEFT JOIN prxgt_odoo_prod */
        $cols = [
            Agg::AS_ODOO_ID_PROD => EntityOdooProduct::ATTR_ODOO_REF
        ];
        $cond = $asOdooProd . '.' . EntityOdooProduct::ATTR_MAGE_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID;
        $result->joinLeft($tblOdooProd, $cond, $cols);
        /* LEFT JOIN prxgt_pv_sale_item */
        $cols = [
            Agg::AS_PV_SUBTOTAL => EntityPvSaleItem::ATTR_SUBTOTAL,
            Agg::AS_PV_DISCOUNT => EntityPvSaleItem::ATTR_DISCOUNT,
            Agg::AS_PV_TOTAL => EntityPvSaleItem::ATTR_TOTAL
        ];
        $cond = $asPvSaleItem . '.' . EntityPvSaleItem::ATTR_SALE_ITEM_ID . '=' . $asSaleItem . '.' . MageEntityOrderItem::ITEM_ID;
        $result->joinLeft($tblPvSaleItem, $cond, $cols);
        /* LEFT JOIN prxgt_pv_stock_item */
        $cols = [
            Agg::AS_PV_UNIT => EntityPvStockItem::ATTR_PV
        ];
        $cond = $asPvStockItem . '.' . EntityPvStockItem::ATTR_STOCK_ITEM_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft($tblPvStockItem, $cond, $cols);
        /* LEFT JOIN prxgt_wrhs_qty_sale */
        $cols = [
            Agg::AS_LOT_QTY => EntityWrhsQtySale::ATTR_TOTAL
        ];
        $cond = $asQtySale . '.' . EntityWrhsQtySale::ATTR_SALE_ITEM_REF . '=' . $asSaleItem . '.' . MageEntityOrderItem::ITEM_ID;
        $result->joinLeft($tblQtySale, $cond, $cols);
        /* LEFT JOIN prxgt_odoo_lot */
        $cols = [
            Agg::AS_ODOO_ID_LOT => EntityOdooLot::ATTR_ODOO_REF
        ];
        $cond = $asOdooLot . '.' . EntityOdooLot::ATTR_MAGE_REF . '=' . $asQtySale . '.' . EntityWrhsQtySale::ATTR_LOT_REF;
        $result->joinLeft($tblOdooLot, $cond, $cols);
        /* WHERE ... */
        $where = $asSaleItem . '.' . MageEntityOrderItem::ORDER_ID . '=:' . self::PARAM_ORDER_ID;
        $result->where($where);
        return $result;
    }

    public function getQueryToSelectCount()
    {
        throw new \Exception("this method is not implemented yet.");
    }
}