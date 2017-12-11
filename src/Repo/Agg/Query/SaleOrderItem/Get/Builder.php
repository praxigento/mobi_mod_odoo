<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get;

use Magento\Sales\Api\Data\OrderItemInterface as EOrderItem;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Repo\Agg\Data\SaleOrderItem as Agg;
use Praxigento\Odoo\Repo\Entity\Data\Lot as EOdooLot;
use Praxigento\Odoo\Repo\Entity\Data\Product as EOdooProduct;
use Praxigento\Pv\Repo\Entity\Data\Sale\Item as EPvSaleItem;
use Praxigento\Pv\Repo\Entity\Data\Stock\Item as EPvStockItem;
use Praxigento\Warehouse\Repo\Entity\Data\Quantity\Sale as EWrhsQtySale;

/**
 * Build query to get Odoo aggregate for SaleOrderItem.
 */
class Builder
    extends \Praxigento\Core\App\Repo\Query\Builder
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
    const BIND_ORDER_ID = 'orderId';
    const BIND_STOCK_ID = 'stockId';

    /**
     * SELECT
     * `saleItem`.`qty_ordered` AS `item_qty`,
     * `saleItem`.`base_discount_amount` AS `price_discount`,
     * `saleItem`.`tax_percent` AS `price_tax_percent`,
     * `saleItem`.`base_row_total` AS `price_total`,
     * `saleItem`.`base_row_total_incl_tax` AS `price_total_with_tax`,
     * `saleItem`.`base_original_price` AS `price_unit_orig`,
     * `saleItem`.`base_price_incl_tax` AS `price_unit`,
     * `odooProd`.`odoo_ref` AS `odoo_id_prod`,
     * `pvSaleItem`.`subtotal` AS `pv_subtotal`,
     * `pvSaleItem`.`discount` AS `pv_discount`,
     * `pvSaleItem`.`total` AS `pv_total`,
     * `pvStockItem`.`pv` AS `pv_unit`,
     * `wrhsQtySale`.`total` AS `lot_qty`,
     * `odooLot`.`odoo_ref` AS `odoo_id_lot`
     * FROM `sales_order_item` AS `saleItem`
     * LEFT JOIN `cataloginventory_stock_item` AS `stockItem`
     * ON stockItem.product_id = saleItem.product_id
     * AND stockItem.stock_id = :stockId
     * LEFT JOIN `prxgt_odoo_prod` AS `odooProd`
     * ON odooProd.mage_ref = saleItem.product_id
     * LEFT JOIN `prxgt_pv_sale_item` AS `pvSaleItem`
     * ON pvSaleItem.sale_item_id = saleItem.product_id
     * LEFT JOIN `prxgt_pv_stock_item` AS `pvStockItem`
     * ON pvStockItem.stock_item_ref = stockItem.item_id
     * LEFT JOIN `prxgt_wrhs_qty_sale` AS `wrhsQtySale`
     * ON wrhsQtySale.sale_item_ref = saleItem.item_id
     * LEFT JOIN `prxgt_odoo_lot` AS `odooLot`
     * ON odooLot.mage_ref = wrhsQtySale.lot_ref
     * WHERE (saleItem.order_id = :orderId)
     *
     * @inheritdoc
     */
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
            self::A_PRICE_UNIT_ORIG => EOrderItem::BASE_PRICE,
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
        $cond = $as . '.' . EOdooProduct::ATTR_MAGE_REF . '=' . $asSaleItem . '.' . Cfg::E_SALE_ORDER_ITEM_A_PRODUCT_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_pv_sale_item */
        $tbl = $this->resource->getTableName(EPvSaleItem::ENTITY_NAME);
        $as = $asPvSale;
        $cols = [
            self::A_PV_SUBTOTAL => EPvSaleItem::ATTR_SUBTOTAL,
            self::A_PV_DISCOUNT => EPvSaleItem::ATTR_DISCOUNT,
            self::A_PV_TOTAL => EPvSaleItem::ATTR_TOTAL
        ];
        $cond = $as . '.' . EPvSaleItem::ATTR_SALE_ITEM_ID . '=' . $asSaleItem . '.' . Cfg::E_SALE_ORDER_ITEM_A_PRODUCT_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_pv_stock_item */
        $tbl = $this->resource->getTableName(EPvStockItem::ENTITY_NAME);
        $as = $asPvStock;
        $cols = [
            self::A_PV_UNIT => EPvStockItem::ATTR_PV
        ];
        $cond = $as . '.' . EPvStockItem::ATTR_STOCK_ITEM_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_wrhs_qty_sale */
        $tbl = $this->resource->getTableName(EWrhsQtySale::ENTITY_NAME);
        $as = $asQty;
        $cols = [
            self::A_LOT_QTY => EWrhsQtySale::ATTR_TOTAL
        ];
        $cond = $as . '.' . EWrhsQtySale::ATTR_SALE_ITEM_REF . '=' . $asSaleItem . '.' . Cfg::E_SALE_ORDER_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_odoo_lot */
        $tbl = $this->resource->getTableName(EOdooLot::ENTITY_NAME);
        $as = $asLot;
        $cols = [
            self::A_ODOO_ID_LOT => EOdooLot::ATTR_ODOO_REF
        ];
        $cond = $as . '.' . EOdooLot::ATTR_MAGE_REF . '=' . $asQty . '.' . EWrhsQtySale::ATTR_LOT_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* WHERE */
        $where = $asSaleItem . '.' . Cfg::E_SALE_ORDER_ITEM_A_ORDER_ID . '=:' . self::BIND_ORDER_ID;
        $result->where($where);

        /* RESULT */
        return $result;
    }

}