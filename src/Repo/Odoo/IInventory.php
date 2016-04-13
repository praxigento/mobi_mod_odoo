<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo;

use Praxigento\Odoo\Api\Data\IBundle;

interface IInventory
{
    /**
     * Get products data from Odoo.
     *
     * @param int[] $ids Odoo IDs of the products to get data.
     * @return IBundle
     */
    public function get($ids = null);

}