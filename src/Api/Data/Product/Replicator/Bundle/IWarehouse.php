<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Product\Replicator\Bundle;


/**
 * Warehouse that is related to bundle products bundle.
 */
interface IWarehouse
{
    /**
     * Currency for warehouse prices ('CNY').
     *
     * @return string|null
     */
    public function getCurrency();

    /**
     * ID of the warehouse in Odoo.
     *
     * @return  int|null
     */
    public function getIdOdoo();

    /**
     * Currency for warehouse prices ('CNY').
     *
     * @param string $data
     */
    public function setCurrency($data);

    /**
     * ID of the warehouse in Odoo.
     *
     * @param int $data
     */
    public function setIdOdoo($data);
}