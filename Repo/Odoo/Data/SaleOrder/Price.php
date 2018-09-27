<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder;

class Price
    extends \Praxigento\Core\Data
{
    /**
     * @return string
     */
    public function getCurrency()
    {
        $result = parent::getCurrency();
        return $result;
    }

    /**
     * @return float
     */
    public function getPaid()
    {
        $result = parent::getPaid();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price\Tax
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
    public function setCurrency($data)
    {
        parent::setCurrency($data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setPaid($data)
    {
        parent::setPaid($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price\Tax $data
     * @return void
     */
    public function setTax($data)
    {
        parent::setTax($data);
    }

}