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
     * Get code used by humans.
     *
     * @return string
     */
    public function getCode();

    /**
     * Get expiration date for all products from this lot.
     *
     * @return string
     */
    public function getExpirationDate();

    /**
     * Get ID of the lot in Odoo.
     *
     * @return  int|null
     */
    public function getIdOdoo();

    /**
     * Set code used by humans.
     *
     * @param string $data
     */
    public function setCode($data);

    /**
     * Set expiration date for all products from this lot.
     *
     * @param string $data
     */
    public function setExpirationDate($data);

    /**
     * Set ID of the lot in Odoo.
     *
     * @param int $data
     */
    public function setIdOdoo($data);
}