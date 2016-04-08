<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo;


interface IPvModule
{
    /**
     * Create new record in wholesale PV register.
     *
     * @param $mageId
     * @param $pv
     */
    public function saveProductWholesalePv($mageId, $pv);

    /**
     * Update wholesale PV in register.
     *
     * @param $mageId
     * @param $pv
     */
    public function updateProductWholesalePv($mageId, $pv);
}