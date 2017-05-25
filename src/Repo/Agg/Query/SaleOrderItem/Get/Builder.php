<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get;

use Magento\Sales\Api\Data\OrderItemInterface as EOrderItem;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Entity\Product as EOdooProduct;
use Praxigento\Odoo\Repo\Agg\Data\SaleOrderItem as Agg;

/**
 * Build query to get Odoo aggregate for SaleOrderItem.
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Builder
{

    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ODOO_LOT = 'odooLot';
    const AS_ODOO_PROD = 'odooProd';
    const AS_PV_SALE_ITEM = 'pvSaleItem';
    const AS_PV_STOCK_ITEM = 'pvStockItem';
    const AS_QTY_SALE = 'wrhsQtySale';
    const AS_SALE_ITEM = 'saleItem';
    const AS_STOCK_ITEM = 'stockItem';

    /** Columns aliases for external usage ('underscore' naming for database fields; 'camelCase' naming for aliases) */
    const A_ITEM_QTY = Agg::AS_ITEM_QTY;
    const A_LOT_QTY = Agg::AS_LOT_QTY;
    const A_ODOO_ID_LOT = Agg::AS_ODOO_ID_LOT;
    const A_ODOO_ID_PROD = Agg::AS_ODOO_ID_PROD;
    const A_PRICE_DISCOUNT = Agg::AS_PRICE_DISCOUNT;
    const A_PRICE_TAX_PERCENT = Agg::AS_PRICE_TAX_PERCENT;
    const A_PRICE_TOTAL = Agg::AS_PRICE_TOTAL;
    const A_PRICE_TOTAL_WITH_TAX = Agg::AS_PRICE_TOTAL_WITH_TAX;
    const A_PRICE_UNIT = Agg::AS_PRICE_UNIT;
    const A_PRICE_UNIT_ORIG = Agg::AS_PRICE_UNIT_ORIG;
    const A_PV_DISCOUNT = Agg::AS_PV_DISCOUNT;
    const A_PV_SUBTOTAL = Agg::AS_PV_SUBTOTAL;
    const A_PV_TOTAL = Agg::AS_PV_TOTAL;
    const A_PV_UNIT = Agg::AS_PV_UNIT;

    /** Bound variables names ('camelCase' naming) */
    const BIND_STOCK_ID = 'stockId';

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* is this a root builder or a queued builder? */
        $result = is_null($source) ? $this->conn->select() : $source;

        /* define tables aliases for internal usage (in this method) */
        $asLot = self::AS_ODOO_LOT;
        $asProd = self::AS_ODOO_PROD;
        $asPvSale = self::AS_PV_SALE_ITEM;
        $asPvStock = self::AS_PV_STOCK_ITEM;
        $asQty = self::AS_QTY_SALE;
        $asSaleItem = self::AS_SALE_ITEM;
        $asStockItem = self::AS_STOCK_ITEM;

        /* FROM sales_order_item */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM);    // name with prefix
        $as = $asSaleItem;    // alias for 'current table' (currently processed in this block of code)
        $cols = [
            self::A_ITEM_QTY => EOrderItem::QTY_ORDERED,
            self::A_PRICE_DISCOUNT => EOrderItem::BASE_DISCOUNT_AMOUNT,
            self::A_PRICE_TAX_PERCENT => EOrderItem::TAX_PERCENT,
            self::A_PRICE_TOTAL => EOrderItem::BASE_ROW_TOTAL,
            self::A_PRICE_TOTAL_WITH_TAX => EOrderItem::BASE_ROW_TOTAL_INCL_TAX,
            self::A_PRICE_UNIT_ORIG => EOrderItem::BASE_ORIGINAL_PRICE,
            self::A_PRICE_UNIT => EOrderItem::BASE_PRICE_INCL_TAX,
        ];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN cataloginventory_stock_item */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM);
        $as = $asStockItem;
        $cols = [];
        $cond = $as . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . $asSaleItem . '.' . EOrderItem::PRODUCT_ID;
        $cond .= ' AND ' . $as . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:' . self::BIND_STOCK_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_odoo_prod */
        $tbl = $this->resource->getTableName(EOdooProduct::ENTITY_NAME);
        $as = $asProd;
        $cols = [
            self::A_ODOO_ID_PROD => EOdooProduct::ATTR_ODOO_REF
        ];
        $cond = $as . '.' . EOdooProduct::ATTR_MAGE_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        //
        return $result;
    }

}