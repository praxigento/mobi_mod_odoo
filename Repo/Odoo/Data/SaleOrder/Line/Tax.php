<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line;

class Tax
    extends \Praxigento\Core\Data
{
    /**
     * @return float
     */
    public function getBase()
    {
        $result = parent::getBase();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate
     */
    public function getRates()
    {
        $result = parent::getRates();
        return $result;
    }

    /**
     * @param float $data
     * @return void
     */
    public function setBase($data)
    {
        parent::setBase($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate $data
     * @return void
     */
    public function setRates($data)
    {
        parent::setRates($data);
    }

}