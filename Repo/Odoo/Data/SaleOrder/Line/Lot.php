<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line;

class Lot
    extends \Praxigento\Core\Data
{
    /**
     * @return int
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
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
     * @param int $data
     * @return void
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setQty($data)
    {
        parent::setQty($data);
    }

}