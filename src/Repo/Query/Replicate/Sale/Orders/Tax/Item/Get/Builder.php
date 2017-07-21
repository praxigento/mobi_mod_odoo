<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Tax\Item\Get;

use Praxigento\Odoo\Config as Cfg;

/**
 * Build query to get sale items tax rates by sale order ID.
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_ITEM_TAX = 'soti';
    const AS_ORDER_TAX = 'sot';

    /** Columns aliases for external usage ('underscore' naming for database fields; 'camelCase' naming for aliases) */
    const A_AMOUNT = Cfg::E_SALE_ORDER_TAX_ITEM_A_REAL_BASE_AMOUNT;
    const A_ORDER_ID = Cfg::E_SALE_ORDER_TAX_A_ORDER_ID;
    const A_SALE_ITEM_ID = Cfg::E_SALE_ORDER_TAX_ITEM_A_ITEM_ID;
    const A_TAX_CODE = Cfg::E_SALE_ORDER_TAX_A_CODE;
    const A_TAX_ID = Cfg::E_SALE_ORDER_TAX_A_TAX_ID;
    const A_TAX_ITEM_ID = Cfg::E_SALE_ORDER_TAX_ITEM_A_TAX_ITEM_ID;
    const A_TAX_PERCENT = Cfg::E_SALE_ORDER_TAX_ITEM_A_TAX_PERCENT;
    const A_TAX_TYPE = Cfg::E_SALE_ORDER_TAX_ITEM_A_TAXABLE_ITEM_TYPE;

    /** Bound variables names ('camelCase' naming) */
    const BIND_ORDER_ID = 'orderId';

    /**
     * SELECT
     * sot.tax_id,
     * sot.order_id,
     * sot.code,
     * soti.tax_item_id,
     * soti.item_id,
     * soti.tax_percent,
     * soti.real_base_amount,
     * soti.taxable_item_type
     * FROM sales_order_tax sot
     * LEFT JOIN sales_order_tax_item soti
     * ON sot.tax_id = soti.tax_id
     * WHERE sot.order_id = :orderId;
     *
     * @param \Magento\Framework\DB\Select|null $source
     */
    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asOrder = self::AS_ORDER_TAX;
        $asItem = self::AS_ITEM_TAX;

        /* SELECT FROM sales_order_tax */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_TAX);
        $as = $asOrder;
        $cols = [
            self::A_TAX_ID => Cfg::E_SALE_ORDER_TAX_A_TAX_ID,
            self::A_ORDER_ID => Cfg::E_SALE_ORDER_TAX_A_ORDER_ID,
            self::A_TAX_CODE => Cfg::E_SALE_ORDER_TAX_A_CODE
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN sales_order_tax_item */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_TAX_ITEM);
        $as = $asItem;
        $cond = $as . '.' . Cfg::E_SALE_ORDER_TAX_ITEM_A_TAX_ID . '=' . $asOrder . '.' . Cfg::E_SALE_ORDER_TAX_A_TAX_ID;
        $cols = [
            self::A_TAX_ITEM_ID => Cfg::E_SALE_ORDER_TAX_ITEM_A_TAX_ITEM_ID,
            self::A_SALE_ITEM_ID => Cfg::E_SALE_ORDER_TAX_ITEM_A_ITEM_ID,
            self::A_TAX_PERCENT => Cfg::E_SALE_ORDER_TAX_ITEM_A_TAX_PERCENT,
            self::A_AMOUNT => Cfg::E_SALE_ORDER_TAX_ITEM_A_REAL_BASE_AMOUNT,
            self::A_TAX_TYPE => Cfg::E_SALE_ORDER_TAX_ITEM_A_TAXABLE_ITEM_TYPE
        ];
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* WHERE */
        $where = $asOrder . '.' . Cfg::E_SALE_ORDER_TAX_A_ORDER_ID . '=:' . self::BIND_ORDER_ID;
        $result->where($where);

        return $result;
    }
}