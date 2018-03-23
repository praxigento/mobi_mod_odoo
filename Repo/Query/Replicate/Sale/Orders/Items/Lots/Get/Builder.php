<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Lots\Get;

use Praxigento\Odoo\Repo\Data\Lot as EOdooLot;
use Praxigento\Warehouse\Repo\Data\Quantity\Sale as EQty;

/**
 * Build query to get lots data for given sale order item to be replicated into Odoo.
 *
 * SELECT
 * `qty`.`sale_item_ref`,
 * `qty`.`total`,
 * `odoo`.`odoo_ref`
 * FROM `prxgt_wrhs_qty_sale` AS `qty`
 * LEFT JOIN `prxgt_odoo_lot` AS `odoo`
 * ON odoo.mage_ref = qty.lot_ref
 * WHERE (qty.sale_item_ref = :itemId)
 *
 */
class Builder
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases */
    const AS_ODOO = 'odoo';
    const AS_QTY_SALES = 'qty';

    /** Columns aliases */
    const A_ITEM_ID = EQty::A_SALE_ITEM_REF;
    const A_ODOO_ID = EOdooLot::A_ODOO_REF;
    const A_TOTAL = EQty::A_TOTAL;

    /** Bound variables names ('camelCase' naming) */
    const BIND_SALE_ITEM_ID = 'itemId';

    /**
     * @param \Magento\Framework\DB\Select|null $source
     */
    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select(); // to build primary queries (started from SELECT)

        /* define tables aliases */
        $asQty = self::AS_QTY_SALES;
        $asOdoo = self::AS_ODOO;

        /* SELECT FROM sales_order */
        $tbl = $this->resource->getTableName(EQty::ENTITY_NAME);
        $as = $asQty;
        $cols = [
            self::A_ITEM_ID => EQty::A_SALE_ITEM_REF,
            self::A_TOTAL => EQty::A_TOTAL
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_odoo_lot */
        $tbl = $this->resource->getTableName(EOdooLot::ENTITY_NAME);
        $as = $asOdoo;
        $cols = [
            self::A_ODOO_ID => EOdooLot::A_ODOO_REF
        ];
        $cond = $as . '.' . EOdooLot::A_MAGE_REF . '=' . $asQty . '.' . EQty::A_LOT_REF;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $result->where($asQty . '.' . EQty::A_SALE_ITEM_REF . '=:' . self::BIND_SALE_ITEM_ID);

        return $result;
    }
}