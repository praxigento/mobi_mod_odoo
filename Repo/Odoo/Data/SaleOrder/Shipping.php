<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder;

class Shipping
    extends \Praxigento\Core\Data
{
    /**
     * @return string
     */
    public function getCode()
    {
        $result = parent::getCode();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping\Tax
     */
    public function getTax()
    {
        $result = parent::getTax();
        return $result;
    }

    /**
     * @param string $data
     * @return void
     */
    public function setCode($data)
    {
        parent::setCode($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping\Tax $data
     * @return void
     */
    public function setTax($data)
    {
        parent::setTax($data);
    }

}