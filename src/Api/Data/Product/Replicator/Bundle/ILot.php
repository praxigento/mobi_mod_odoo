<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Product\Replicator\Bundle;

/**
 * Lot that is related to bundle products bundle.
 */
interface ILot
{
    /**
     * Expiration date for all products from this lot.
     *
     * @return string|null
     */
    public function getExpirationDate();

    /**
     * ID of the lot in Odoo.
     *
     * @return  int|null
     */
    public function getIdOdoo();

    /**
     * Expiration date for all products from this lot.
     *
     * @param string $data
     */
    public function setExpirationDate($data);

    /**
     * ID of the lot in Odoo.
     *
     * @param int $data
     */
    public function setIdOdoo($data);
}