<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price;

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
     * @return float
     */
    public function getTotal()
    {
        $result = parent::getTotal();
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
     * @param float $data
     * @return void
     */
    public function setTotal($data)
    {
        parent::setTotal($data);
    }

}