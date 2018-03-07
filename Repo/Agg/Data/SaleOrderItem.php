<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Data;

/**
 * Aggregate for Sale Order Item data to be replicated to Odoo. There are one or more aggregated lines for one Magento Sale Order Item (by lots used in the sale).
 */
class SaleOrderItem
    extends \Praxigento\Core\Data
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_ITEM_QTY = 'item_qty';
    const AS_LOT_QTY = 'lot_qty';
    const AS_ODOO_ID_LOT = 'odoo_id_lot';
    const AS_ODOO_ID_PROD = 'odoo_id_prod';
    const AS_PRICE_DISCOUNT = 'price_discount';
    const AS_PRICE_TAX_PERCENT = 'price_tax_percent';
    const AS_PRICE_TOTAL = 'price_total';
    const AS_PRICE_TOTAL_WITH_TAX = 'price_total_with_tax';
    const AS_PRICE_UNIT = 'price_unit';
    const AS_PRICE_UNIT_ORIG = 'price_unit_orig';
    const AS_PV_DISCOUNT = 'pv_discount';
    const AS_PV_SUBTOTAL = 'pv_subtotal';
    const AS_PV_TOTAL = 'pv_total';
    const AS_PV_UNIT = 'pv_unit';

    /**#@- */

    public function getItemQty()
    {
        $result = parent::get(static::AS_ITEM_QTY);
        return $result;
    }

    public function getLotQty()
    {
        $result = parent::get(static::AS_LOT_QTY);
        return $result;
    }

    public function getOdooIdLot()
    {
        $result = parent::get(static::AS_ODOO_ID_LOT);
        return $result;
    }

    public function getOdooIdProduct()
    {
        $result = parent::get(static::AS_ODOO_ID_PROD);
        return $result;
    }

    public function getPriceDiscount()
    {
        $result = parent::get(static::AS_PRICE_DISCOUNT);
        return $result;
    }

    public function getPriceTax()
    {
        $result = parent::get(static::AS_PRICE_TOTAL_WITH_TAX);
        return $result;
    }

    public function getPriceTaxPercent()
    {
        $result = parent::get(static::AS_PRICE_TAX_PERCENT);
        return $result;
    }

    public function getPriceTotal()
    {
        $result = parent::get(static::AS_PRICE_TOTAL);
        return $result;
    }

    public function getPriceTotalWithTax()
    {
        $result = parent::get(static::AS_PRICE_TOTAL_WITH_TAX);
        return $result;
    }

    public function getPriceUnit()
    {
        $result = parent::get(static::AS_PRICE_UNIT);
        return $result;
    }

    public function getPriceUnitOrig()
    {
        $result = parent::get(static::AS_PRICE_UNIT_ORIG);
        return $result;
    }

    public function getPvDiscount()
    {
        $result = parent::get(static::AS_PV_DISCOUNT);
        return $result;
    }

    public function getPvSubtotal()
    {
        $result = parent::get(static::AS_PV_SUBTOTAL);
        return $result;
    }

    public function getPvTotal()
    {
        $result = parent::get(static::AS_PV_TOTAL);
        return $result;
    }

    public function getPvUnit()
    {
        $result = parent::get(static::AS_PV_UNIT);
        return $result;
    }


    public function setItemQty($data)
    {
        parent::set(static::AS_ITEM_QTY, $data);
    }

    public function setLotQty($data)
    {
        parent::set(static::AS_LOT_QTY, $data);
    }

    public function setOdooIdLot($data)
    {
        parent::set(static::AS_ODOO_ID_LOT, $data);
    }

    public function setOdooIdProduct($data)
    {
        parent::set(static::AS_ODOO_ID_PROD, $data);
    }

    public function setPriceDiscount($data)
    {
        parent::set(static::AS_PRICE_DISCOUNT, $data);
    }

    public function setPriceTax($data)
    {
        parent::set(static::AS_PRICE_TOTAL_WITH_TAX, $data);
    }

    public function setPriceTaxPercent($data)
    {
        parent::set(static::AS_PRICE_TAX_PERCENT, $data);
    }

    public function setPriceTotal($data)
    {
        parent::set(static::AS_PRICE_TOTAL, $data);
    }

    public function setPriceTotalWithTax($data)
    {
        parent::set(static::AS_PRICE_TOTAL_WITH_TAX, $data);
    }

    public function setPriceUnit($data)
    {
        parent::set(static::AS_PRICE_UNIT, $data);
    }

    public function setPriceUnitOrig($data)
    {
        parent::set(static::AS_PRICE_UNIT_ORIG, $data);
    }

    public function setPvDiscount($data)
    {
        parent::set(static::AS_PV_DISCOUNT, $data);
    }

    public function setPvSubtotal($data)
    {
        parent::set(static::AS_PV_SUBTOTAL, $data);
    }

    public function setPvTotal($data)
    {
        parent::set(static::AS_PV_TOTAL, $data);
    }

    public function setPvUnit($data)
    {
        parent::set(static::AS_PV_UNIT, $data);
    }


}