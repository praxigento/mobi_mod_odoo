<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle;

/**
 * Lot that is related to products bundle.
 *
 * @api
 */
interface ILot
{
    /**
     * Get expiration date for all products from this lot.
     *
     * @api
     * @return string
     */
    public function getExpirationDate();

    /**
     * Get ID of the lot in Odoo.
     *
     * @api
     * @return  int|null
     */
    public function getIdOdoo();

    /**
     * Set expiration date for all products from this lot.
     *
     * @api
     * @param string $data
     */
    public function setExpirationDate($data);

    /**
     * Set ID of the lot in Odoo.
     *
     * @api
     * @param int $data
     */
    public function setIdOdoo($data);
}