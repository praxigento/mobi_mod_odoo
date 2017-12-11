<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Request;

class ProductsFromOdoo extends \Praxigento\Core\App\Service\Base\Request
{
    /**
     * Get Odoo IDs of the products to replicate.
     * @return int[]|int
     */
    public function getOdooIds()
    {
        $result = parent::getOdooIds();
        return $result;
    }

    /**
     * Set Odoo IDs of the products to replicate.
     * @param int[]|int $data
     */
    public function setOdooIds($data)
    {
        parent::setOdooIds($data);
    }
}