<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity;

interface IWarehouse extends \Praxigento\Core\Repo\IEntity
{
    /**
     * Get the data instance by ID (ID can be an array for complex primary keys).
     *
     * @param int $id
     * @return \Praxigento\Odoo\Data\Entity\Warehouse|bool Found instance data or 'false'
     */
    public function getById($id);
}