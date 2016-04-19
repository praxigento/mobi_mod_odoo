<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Api\Bundle;

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
     * @return string
     */
    public function getCurrency();

    /**
     * Set currency for wholesale prices ('EUR').
     *
     * @param string $data
     */
    public function setCurrency($data);
}