<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Api\Bundle;

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
     * Get Odoo ID of the lot.
     *
     * @return  int|null
     */
    public function getId();

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
     * Set Odoo ID of the lot.
     *
     * @param int $data
     */
    public function setId($data);
}