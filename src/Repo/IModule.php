<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo;


interface IModule
{
    /**
     * Retrieve ID for the category to place new products into.
     * @return int
     */
    public function getCategoryIdToPlaceNewProduct();
}