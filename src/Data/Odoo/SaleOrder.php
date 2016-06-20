<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Odoo;

use Flancer32\Lib\DataObject;

class SaleOrder extends DataObject
{


    /**
     * @return Contact
     */
    public function getAddrBilling()
    {
        $result = parent::getAddrBilling();
        return $result;
    }

    /**
     * @return Contact
     */
    public function getAddrShipping()
    {
        $result = parent::getAddrShipping();
        return $result;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        $result = parent::getClientId();
        return $result;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        $result = parent::getDate();
        return $result;
    }

    /**
     * @return SaleOrder\Line[]
     */
    public function getLines()
    {
        $result = parent::getLines();
        return $result;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        $result = parent::getNumber();
        return $result;
    }

    /**
     * @return Payment[]
     */
    public function getPayments()
    {
        $result = parent::getPayments();
        return $result;
    }

    /**
     * @return double
     */
    public function getPriceDiscountAdditional()
    {
        $result = parent::getPriceDiscountAdditional();
        return $result;
    }

    /**
     * @return double
     */
    public function getPriceTax()
    {
        $result = parent::getPriceTax();
        return $result;
    }

    /**
     * @return string
     */
    public function getShippingMethod()
    {
        $result = parent::getShippingMethod();
        return $result;
    }

    /**
     * @return double
     */
    public function getShippingPrice()
    {
        $result = parent::getShippingPrice();
        return $result;
    }

    /**
     * @return int
     */
    public function getWarehouseId()
    {
        $result = parent::getWarehouseId();
        return $result;
    }

    /**
     * @param Contact $data
     */
    public function setAddrBilling($data = null)
    {
        parent::setAddrBilling($data);
    }

    /**
     * @param Contact $data
     */
    public function setAddrShipping($data = null)
    {
        parent::setAddrShipping($data);
    }

    /**
     * @param string $data
     */
    public function setClientId($data = null)
    {
        parent::setClientId($data);
    }

    /**
     * @param string $data
     */
    public function setDate($data = null)
    {
        parent::setDate($data);
    }

    /**
     * @param SaleOrder\Line[] $data
     */
    public function setLines($data = null)
    {
        parent::setLines($data);
    }

    /**
     * @param string $data
     */
    public function setNumber($data = null)
    {
        parent::setNumber($data);
    }

    /**
     * @param Payment[] $data
     */
    public function setPayments($data = null)
    {
        parent::setPayments($data);
    }

    /**
     * @param double $data
     */
    public function setPriceDiscountAdditional($data = null)
    {
        parent::setPriceDiscountAdditional($data);
    }

    /**
     * @param string $data
     */
    public function setPriceTax($data = null)
    {
        parent::setPriceTax($data);
    }

    /**
     * @param string $data
     */
    public function setShippingMethod($data = null)
    {
        parent::setShippingMethod($data);
    }

    /**
     * @param double $data
     */
    public function setShippingPrice($data = null)
    {
        parent::setShippingPrice($data);
    }

    /**
     * @param int $data
     */
    public function setWarehouseId($data = null)
    {
        parent::setWarehouseId($data);
    }

}