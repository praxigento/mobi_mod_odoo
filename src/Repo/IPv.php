<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo;


interface IPv
{
    /**
     * @param int $stockItemMageId
     * @return  double $pv
     */
    public function getWarehousePv($stockItemMageId);

    /**
     * Create new record for wholesale PV in register.
     *
     * @param int $productMageId
     * @param double $pv
     */
    public function registerProductWholesalePv($productMageId, $pv);

    /**
     * @param int $stockItemMageId
     * @param double $pv
     */
    public function registerWarehousePv($stockItemMageId, $pv);

    /**
     * Update wholesale PV in register.
     *
     * @param int $productMageId
     * @param double $pv
     */
    public function updateProductWholesalePv($productMageId, $pv);

    /**
     * @param int $stockItemMageId
     * @param double $pv
     */
    public function updateWarehousePv($stockItemMageId, $pv);
}