<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo;

interface IInventory
{
    /**
     * Get products data from Odoo.
     *
     * @param int[] $ids Odoo IDs of the products to get data.
     * @return \Praxigento\Odoo\Data\Odoo\Inventory
     */
    public function get($ids = null);

}