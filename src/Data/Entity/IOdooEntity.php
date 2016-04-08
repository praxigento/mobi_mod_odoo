<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Entity;


interface IOdooEntity
{
    const ATTR_MAGE_REF = 'mage_ref';
    const ATTR_ODOO_REF = 'odoo_ref';

    /**
     * Get Mage ID for the entity.
     *
     * @return int
     */
    public function getMageRef();

    /**
     * Get Odoo ID for the entity.
     *
     * @return int
     */
    public function getOdooRef();

    /**
     * Set Mage ID for the entity.
     * @param int $data
     */
    public function setMageRef($data);

    /**
     * Set Odoo ID for the entity.
     *
     * @param int $data
     */
    public function setOdooRef($data);
}