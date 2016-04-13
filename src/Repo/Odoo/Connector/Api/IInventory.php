<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Api;


interface IInventory
{
    /**
     * Get products data from Odoo.
     *
     * @param int[] $ids Odoo IDs of the products to get data.
     * @return mixed
     */
    public function get($ids = null);

}