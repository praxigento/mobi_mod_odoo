<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder;

class Line
    extends \Praxigento\Core\Data
{
    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line\Lot[]
     */
    public function getLots()
    {
        $result = parent::getLots();
        return $result;
    }

    /**
     * @return int
     */
    public function getProductIdOdoo()
    {
        $result = parent::getProductIdOdoo();
        return $result;
    }

    /**
     * @return float
     */
    public function getPv()
    {
        $result = parent::getPv();
        return $result;
    }

    /**
     * @return float
     */
    public function getQty()
    {
        $result = parent::getQty();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line\Tax
     */
    public function getTax()
    {
        $result = parent::getTax();
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line\Lot[] $data
     * @return void
     */
    public function setLots($data)
    {
        parent::setLots($data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setProductIdOdoo($data)
    {
        parent::setProductIdOdoo($data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setPv($data)
    {
        parent::setPv($data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setQty($data)
    {
        parent::setQty($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line\Tax $data
     * @return void
     */
    public function setTax($data)
    {
        parent::setTax($data);
    }

}