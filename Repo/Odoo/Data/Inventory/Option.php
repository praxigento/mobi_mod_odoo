<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\Inventory;

/**
 * Options that are related to products bundle.
 *
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 *
 * @deprecated MOBI-765
 */
class Option
    extends \Praxigento\Core\Data
{
    /**
     * Get currency for wholesale prices ('EUR').
     *
     * @return string
     */
    public function getCurrency()
    {
        $result = parent::getCurrency();
        return $result;
    }

    /**
     * Set currency for wholesale prices ('EUR').
     *
     * @param string $data
     * @return void
     */
    public function setCurrency($data)
    {
        parent::setCurrency($data);
    }
}