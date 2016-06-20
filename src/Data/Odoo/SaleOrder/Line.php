<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Odoo\SaleOrder;

use Flancer32\Lib\DataObject;

class Line extends DataObject
{
    public function getLotId()
    {
        $result = parent::getLotId();
        return $result;
    }

    public function getPriceActual()
    {
        $result = parent::getPriceActual();
        return $result;
    }

    public function getPriceAdjusted()
    {
        $result = parent::getPriceAdjusted();
        return $result;
    }

    public function getPriceDiscount()
    {
        $result = parent::getPriceDiscount();
        return $result;
    }

    public function getProductId()
    {
        $result = parent::getProductId();
        return $result;
    }

    public function getPvActual()
    {
        $result = parent::getPvActual();
        return $result;
    }

    public function getPvDiscount()
    {
        $result = parent::getPvDiscount();
        return $result;
    }

    public function getQty()
    {
        $result = parent::getQty();
        return $result;
    }

    public function setLotId($data = null)
    {
        parent::setLotId($data);
    }

    public function setPriceActual($data = null)
    {
        parent::setPriceActual($data);
    }

    public function setPriceAdjusted($data = null)
    {
        parent::setPriceAdjusted($data);
    }

    public function setPriceDiscount($data = null)
    {
        parent::setPriceDiscount($data);
    }

    public function setProductId($data = null)
    {
        parent::setProductId($data);
    }

    public function setPvActual($data = null)
    {
        parent::setPvActual($data);
    }

    public function setPvDiscount($data = null)
    {
        parent::setPvDiscount($data);
    }

    public function setQty($data = null)
    {
        parent::setQty($data);
    }

}