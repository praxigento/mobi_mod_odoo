<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Agg;

use Flancer32\Lib\DataObject;

/**
 * Aggregate for Sale Order Item data to be replicated to Odoo.
 */
class SaleOrderItem extends DataObject
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_ITEM_DISCOUNT_PRICE = 'item_discount_price';
    const AS_ITEM_DISCOUNT_PV = 'item_discount_pv';
    const AS_ITEM_QTY = 'item_qty';
    const AS_LOT_QTY = 'lot_qty';
    const AS_ODOO_ID_LOT = 'odoo_id_lot';
    const AS_ODOO_ID_PROD = 'odoo_id_prod';
    const AS_PRICE = 'price';
    const AS_PV_DISCOUNT = 'pv_discount';
    const AS_PV_SUBTOTAL = 'pv_subtotal';
    const AS_PV_TOTAL = 'pv_total';
    /**#@- */
    
    public function getItemDiscountPrice()
    {
        $result = parent::getData(static::AS_ITEM_DISCOUNT_PRICE);
        return $result;
    }

    public function getItemDiscountPv()
    {
        $result = parent::getData(static::AS_ITEM_DISCOUNT_PV);
        return $result;
    }

    public function getItemQty()
    {
        $result = parent::getData(static::AS_ITEM_QTY);
        return $result;
    }

    public function getLotQty()
    {
        $result = parent::getData(static::AS_LOT_QTY);
        return $result;
    }

    public function getOdooIdLot()
    {
        $result = parent::getData(static::AS_ODOO_ID_LOT);
        return $result;
    }

    public function getOdooIdProduct()
    {
        $result = parent::getData(static::AS_ODOO_ID_PROD);
        return $result;
    }

    public function getPrice()
    {
        $result = parent::getData(static::AS_PRICE);
        return $result;
    }

    public function getPvDiscount()
    {
        $result = parent::getData(static::AS_PV_DISCOUNT);
        return $result;
    }

    public function getPvSubtotal()
    {
        $result = parent::getData(static::AS_PV_SUBTOTAL);
        return $result;
    }

    public function getPvTotal()
    {
        $result = parent::getData(static::AS_PV_TOTAL);
        return $result;
    }

    public function setItemDiscountPrice($data)
    {
        parent::setData(static::AS_ITEM_DISCOUNT_PRICE, $data);
    }

    public function setItemDiscountPv($data)
    {
        parent::setData(static::AS_ITEM_DISCOUNT_PV, $data);
    }

    public function setItemQty($data)
    {
        parent::setData(static::AS_ITEM_QTY, $data);
    }

    public function setLotQty($data)
    {
        parent::setData(static::AS_LOT_QTY, $data);
    }

    public function setOdooIdLot($data)
    {
        parent::setData(static::AS_ODOO_ID_LOT, $data);
    }

    public function setOdooIdProduct($data)
    {
        parent::setData(static::AS_ODOO_ID_PROD, $data);
    }

    public function setPrice($data)
    {
        parent::setData(static::AS_PRICE, $data);
    }

    public function setPvDiscount($data)
    {
        parent::setData(static::AS_PV_DISCOUNT, $data);
    }

    public function setPvSubtotal($data)
    {
        parent::setData(static::AS_PV_SUBTOTAL, $data);
    }

    public function setPvTotal($data)
    {
        parent::setData(static::AS_PV_TOTAL, $data);
    }


}