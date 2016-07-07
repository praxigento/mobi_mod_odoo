<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Agg;

use Flancer32\Lib\DataObject;

/**
 * Aggregate for Sale Order Item data to be replicated to Odoo. There are one or more aggregated lines for one Magento Sale Order Item (by lots used in the sale).
 */
class SaleOrderItem extends DataObject
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_ITEM_QTY = 'item_qty';
    const AS_LOT_QTY = 'lot_qty';
    const AS_ODOO_ID_LOT = 'odoo_id_lot';
    const AS_ODOO_ID_PROD = 'odoo_id_prod';
    const AS_PRICE_DISCOUNT = 'price_discount';
    const AS_PRICE_TOTAL = 'price_total';
    const AS_PRICE_UNIT = 'price_unit';
    const AS_PV_DISCOUNT = 'pv_discount';
    const AS_PV_SUBTOTAL = 'pv_subtotal';
    const AS_PV_TOTAL = 'pv_total';
    const AS_PV_UNIT = 'pv_unit';

    /**#@- */

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

    public function getPriceDiscount()
    {
        $result = parent::getData(static::AS_PRICE_DISCOUNT);
        return $result;
    }

    public function getPriceTotal()
    {
        $result = parent::getData(static::AS_PRICE_TOTAL);
        return $result;
    }

    public function getPriceUnit()
    {
        $result = parent::getData(static::AS_PRICE_UNIT);
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

    public function getPvUnit()
    {
        $result = parent::getData(static::AS_PV_UNIT);
        return $result;
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

    public function setPriceDiscount($data)
    {
        parent::setData(static::AS_PRICE_DISCOUNT, $data);
    }

    public function setPriceTotal($data)
    {
        parent::setData(static::AS_PRICE_TOTAL, $data);
    }

    public function setPriceUnit($data)
    {
        parent::setData(static::AS_PRICE_UNIT, $data);
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

    public function setPvUnit($data)
    {
        parent::setData(static::AS_PV_UNIT, $data);
    }


}