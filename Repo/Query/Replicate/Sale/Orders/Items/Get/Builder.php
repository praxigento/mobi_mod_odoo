<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Get;

use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Repo\Data\Product as EProd;
use Praxigento\Pv\Repo\Data\Sale\Item as EPv;

/**
 * Build query to get sale order items data to be replicated into Odoo.
 *
 * SELECT
 * `items`.`item_id`,
 * `items`.`sku`,
 * `items`.`product_id`,
 * `items`.`qty_ordered`,
 * `items`.`base_price`,
 * `items`.`tax_percent`,
 * `items`.`base_row_total_incl_tax`,
 * `odoo`.`odoo_ref`,
 * `pv`.`subtotal` AS `pvSubtotal`,
 * `pv`.`discount` AS `pvDiscount`
 * FROM `sales_order_item` AS `items`
 * LEFT JOIN `prxgt_odoo_prod` AS `odoo`
 * ON odoo.mage_ref = items.product_id
 * LEFT JOIN `prxgt_pv_sale_item` AS `pv`
 * ON pv.sale_item_id = items.item_id
 * WHERE (items.order_id = :orderId)
 *
 * @deprecated remove it
 */
class Builder
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases */
    const AS_ODOO = 'odoo';
    const AS_ORDER_ITEM = 'items';
    const AS_PV = 'pv';

    /** Columns aliases */
    const A_BASE_PRICE = Cfg::E_SALE_ORDER_ITEM_A_BASE_PRICE;
    const A_BASE_ROW_TOTAL_INCL_TAX = Cfg::E_SALE_ORDER_ITEM_A_BASE_ROW_TOTAL_INCL_TAX;
    const A_ITEM_ID = Cfg::E_SALE_ORDER_ITEM_A_ITEM_ID;
    const A_ODOO_REF = EProd::A_ODOO_REF;
    const A_PRODUCT_ID = Cfg::E_SALE_ORDER_ITEM_A_PRODUCT_ID;
    const A_PV_DISCOUNT = 'pvDiscount';
    const A_PV_SUBTOTAL = 'pvSubtotal';
    const A_QTY_ORDERED = Cfg::E_SALE_ORDER_ITEM_A_QTY_ORDERED;
    const A_SKU = Cfg::E_SALE_ORDER_ITEM_A_SKU;
    const A_TAX_PERCENT = Cfg::E_SALE_ORDER_ITEM_A_TAX_PERCENT;

    /** Bound variables names ('camelCase' naming) */
    const BIND_ORDER_ID = 'orderId';

    /**
     * @param \Magento\Framework\DB\Select|null $source
     */
    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select(); // to build primary queries (started from SELECT)

        /* define tables aliases */
        $asItem = self::AS_ORDER_ITEM;
        $asOdoo = self::AS_ODOO;
        $asPv = self::AS_PV;

        /* SELECT FROM sales_order */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM);
        $as = $asItem;
        $cols = [
            self::A_ITEM_ID => Cfg::E_SALE_ORDER_ITEM_A_ITEM_ID,
            self::A_SKU => Cfg::E_SALE_ORDER_ITEM_A_SKU,
            self::A_PRODUCT_ID => Cfg::E_SALE_ORDER_ITEM_A_PRODUCT_ID,
            self::A_QTY_ORDERED => Cfg::E_SALE_ORDER_ITEM_A_QTY_ORDERED,
            self::A_BASE_PRICE => Cfg::E_SALE_ORDER_ITEM_A_BASE_PRICE,
            self::A_TAX_PERCENT => Cfg::E_SALE_ORDER_ITEM_A_TAX_PERCENT,
            self::A_BASE_ROW_TOTAL_INCL_TAX => Cfg::E_SALE_ORDER_ITEM_A_BASE_ROW_TOTAL_INCL_TAX
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_odoo_prod */
        $tbl = $this->resource->getTableName(EProd::ENTITY_NAME);
        $as = $asOdoo;
        $cols = [
            self::A_ODOO_REF => EProd::A_ODOO_REF
        ];
        $cond = $as . '.' . EProd::A_MAGE_REF . '=' . $asItem . '.' . Cfg::E_SALE_ORDER_ITEM_A_PRODUCT_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_pv_stock_item */
        $tbl = $this->resource->getTableName(EPv::ENTITY_NAME);
        $as = $asPv;
        $cols = [
            self::A_PV_SUBTOTAL => EPv::A_SUBTOTAL,
            self::A_PV_DISCOUNT => EPv::A_DISCOUNT
        ];
        $cond = $as . '.' . EPv::A_ITEM_REF . '=' . $asItem . '.' . Cfg::E_SALE_ORDER_ITEM_A_ITEM_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $result->where($asItem . '.' . Cfg::E_SALE_ORDER_ITEM_A_ORDER_ID . '=:' . self::BIND_ORDER_ID);

        return $result;
    }
}