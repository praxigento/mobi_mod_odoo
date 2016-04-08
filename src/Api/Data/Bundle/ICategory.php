<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle;

/**
 * Category that is related to products bundle.
 *
 * @api
 */
interface ICategory
{


    /**
     * Get Odoo ID of the category.
     *
     * @return  int|null
     */
    public function getId();

    /**
     * Get category name (not localized).
     *
     * @return string
     */
    public function getName();

    /**
     * Get Odoo ID of the parent category.
     *
     * @return  int|null
     */
    public function getParentId();

    /**
     * Set Odoo ID of the category.
     *
     * @param int $data
     */
    public function setId($data);

    /**
     * Set category name (not localized).
     *
     * @param string $data
     */
    public function setName($data);

    /**
     * Set Odoo ID of the parent category.
     *
     * @param int $data
     */
    public function setParentId($data);
}