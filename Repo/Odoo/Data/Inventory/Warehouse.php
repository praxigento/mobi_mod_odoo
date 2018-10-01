<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\Inventory;

/**
 * Warehouse that is related to products bundle.
 *
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 *
 */
class Warehouse
    extends \Praxigento\Core\Data
{
    /**
     * Get short code to identify warehouse by humans.
     *
     * @return string
     */
    public function getCode()
    {
        $result = parent::getCode();
        return $result;
    }

    /**
     * Get currency for warehouse prices ('CNY').
     *
     * @return string
     */
    public function getCurrency()
    {
        $result = parent::getCurrency();
        return $result;
    }

    /**
     * Get Odoo ID of the warehouse.
     *
     * @return  string
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
        return $result;
    }

    /**
     * Set short code to identify warehouse by humans.
     *
     * @param string $data
     * @return void
     */
    public function setCode($data)
    {
        parent::setCode($data);
    }

    /**
     * Set currency for warehouse prices ('EUR').
     *
     * @param string $data
     * @return void
     */
    public function setCurrency($data)
    {
        parent::setCurrency($data);
    }

    /**
     * Set Odoo ID of the warehouse.
     *
     * @param string $data
     * @return void
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }
}