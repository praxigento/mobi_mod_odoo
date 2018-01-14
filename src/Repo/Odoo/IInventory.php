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
     * @param int[] $prodIds Odoo IDs of the products to get data for.
     * @param string[] $wrhsIds Business codes of the warehouses to get data from.
     * @return \Praxigento\Odoo\Data\Odoo\Inventory
     */
    public function get($prodIds = null, $wrhsIds = null);

}