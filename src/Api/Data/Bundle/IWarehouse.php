<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle;


/**
 * Warehouse that is related to products bundle.
 * 
 * @api
 */
interface IWarehouse
{
    /**
     * Get short code to identify warehouse by humans.
     *
     * @api
     * @return string
     */
    public function getCode();

    /**
     * Get currency for warehouse prices ('CNY').
     *
     * @api
     * @return string
     */
    public function getCurrency();

    /**
     * Get ID of the warehouse in Odoo.
     *
     * @api
     * @return  int|null
     */
    public function getIdOdoo();

    /**
     * Set short code to identify warehouse by humans.
     *
     * @api
     * @param string $data
     */
    public function setCode($data);

    /**
     * Set currency for warehouse prices ('CNY').
     *
     * @api
     * @param string $data
     */
    public function setCurrency($data);

    /**
     * Set ID of the warehouse in Odoo.
     *
     * @api
     * @param int $data
     */
    public function setIdOdoo($data);
}