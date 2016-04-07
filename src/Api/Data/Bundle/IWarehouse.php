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
     * Get Odoo ID of the warehouse.
     *
     * @api
     * @return  int|null
     */
    public function getId();

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
     * Set Odoo ID of the warehouse.
     *
     * @api
     * @param int $data
     */
    public function setId($data);
}