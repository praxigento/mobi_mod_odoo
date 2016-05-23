<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo;


interface IPv
{
    /**
     * @param int $stockItemMageRef
     * @return  double $pv
     */
    public function getWarehousePv($stockItemMageRef);

    /**
     * Create new record for wholesale PV in register.
     *
     * @param int $productMageId
     * @param double $pv
     */
    public function registerProductWholesalePv($productMageId, $pv);

    /**
     * @param int $stockItemMageRef
     * @param double $pv
     */
    public function registerWarehousePv($stockItemMageRef, $pv);

    /**
     * Update wholesale PV in register.
     *
     * @param int $productMageRef
     * @param double $pv
     */
    public function updateProductWholesalePv($productMageRef, $pv);

    /**
     * @param int $stockItemMageRef
     * @param double $pv
     */
    public function updateWarehousePv($stockItemMageRef, $pv);
}