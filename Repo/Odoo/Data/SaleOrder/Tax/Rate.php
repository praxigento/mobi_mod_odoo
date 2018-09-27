<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax;

class Rate
    extends \Praxigento\Core\Data
{
    /**
     * @return float
     */
    public function getAmount()
    {
        $result = parent::getAmount();
        return $result;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        $result = parent::getCode();
        return $result;
    }

    /**
     * @return float
     */
    public function getPercent()
    {
        $result = parent::getPercent();
        return $result;
    }

    /**
     * @param float $data
     * @return void
     */
    public function setAmount($data)
    {
        parent::setAmount($data);
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
     * @param float $data
     * @return void
     */
    public function setPercent($data)
    {
        parent::setPercent($data);
    }

}