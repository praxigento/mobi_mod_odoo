<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle;

/**
 * Options that are related to products bundle.
 *
 * @api
 */
interface IOption
{
    /**
     * Get currency for wholesale prices ('EUR').
     *
     * @api
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency for wholesale prices ('EUR').
     *
     * @api
     *
     * @param string $data
     */
    public function setCurrency($data);
}