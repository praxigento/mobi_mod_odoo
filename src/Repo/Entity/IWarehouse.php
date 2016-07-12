<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity;

interface IWarehouse
    extends \Praxigento\Odoo\Repo\Entity\IOdooEntity
{
    /**
     * @inheritdoc
     *
     * @param int $id
     * @return \Praxigento\Odoo\Data\Entity\Warehouse|bool Found instance data or 'false'
     */
    public function getById($id);
}