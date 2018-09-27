<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Data;

class SaleOrder
    extends \Praxigento\Core\Data
{
    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Contact
     */
    public function getAddrBilling()
    {
        $result = parent::getAddrBilling();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Contact
     */
    public function getAddrShipping()
    {
        $result = parent::getAddrShipping();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Customer
     */
    public function getCustomer()
    {
        $result = parent::getCustomer();
        return $result;
    }

    /**
     * @return string
     */
    public function getDatePaid()
    {
        $result = parent::getDatePaid();
        return $result;
    }

    /**
     * @return int
     */
    public function getIdMage()
    {
        $result = parent::getIdMage();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line[]
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
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Payment[]
     */
    public function getPayments()
    {
        $result = parent::getPayments();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price
     */
    public function getPrice()
    {
        $result = parent::getPrice();
        return $result;
    }

    /**
     * @return float
     */
    public function getPvTotal()
    {
        $result = parent::getPvTotal();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping
     */
    public function getShipping()
    {
        $result = parent::getShipping();
        return $result;
    }

    /**
     * @return int
     */
    public function getWarehouseIdOdoo()
    {
        $result = parent::getWarehouseIdOdoo();
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Contact $data
     * @return void
     */
    public function setAddrBilling($data)
    {
        parent::setAddrBilling($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Contact $data
     * @return void
     */
    public function setAddrShipping($data)
    {
        parent::setAddrShipping($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Customer $data
     * @return void
     */
    public function setCustomer($data)
    {
        parent::setCustomer($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDatePaid($data)
    {
        parent::setDatePaid($data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setIdMage($data)
    {
        parent::setIdMage($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line[] $data
     * @return void
     */
    public function setLines($data)
    {
        parent::setLines($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setNumber($data)
    {
        parent::setNumber($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Payment[] $data
     * @return void
     */
    public function setPayments($data)
    {
        parent::setPayments($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price $data
     * @return void
     */
    public function setPrice($data)
    {
        parent::setPrice($data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setPvTotal($data)
    {
        parent::setPvTotal($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping $data
     * @return void
     */
    public function setShipping($data)
    {
        parent::setShipping($data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setWarehouseIdOdoo($data)
    {
        parent::setWarehouseIdOdoo($data);
    }

}