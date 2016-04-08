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
     * @return string
     */
    public function getCode();

    /**
     * Get currency for warehouse prices ('CNY').
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Get Odoo ID of the warehouse.
     *
     * @return  int|null
     */
    public function getId();

    /**
     * Set short code to identify warehouse by humans.
     *
     * @param string $data
     */
    public function setCode($data);

    /**
     * Set currency for warehouse prices ('CNY').
     *
     * @param string $data
     */
    public function setCurrency($data);

    /**
     * Set Odoo ID of the warehouse.
     *
     * @param int $data
     */
    public function setId($data);
}