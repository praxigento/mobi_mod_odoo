<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data;

class Payment
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
     * @return string
     */
    public function getCurrency()
    {
        $result = parent::getCurrency();
        return $result;
    }

    /**
     * @param string $data
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
     * @param string $data
     * @return void
     */
    public function setCurrency($data)
    {
        parent::setCurrency($data);
    }

}