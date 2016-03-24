<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Product\Replicator\Bundle;

/**
 * Options that are related to bundle products bundle.
 */
interface IOption
{
    /**
     * Currency for wholesale prices ('EUR').
     *
     * @return string|null
     */
    public function getCurrency();

    /**
     * Currency for wholesale prices ('EUR').
     *
     * @param string $data
     */
    public function setCurrency($data);
}