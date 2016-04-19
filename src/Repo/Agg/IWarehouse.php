<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg;

use Praxigento\Odoo\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Warehouse\Repo\Agg\IWarehouse as WrhsIWarehouse;

interface IWarehouse extends WrhsIWarehouse
{
    const AS_ODOO = 'pow';

    /**
     * @param AggWarehouse $data
     * @return AggWarehouse
     */
    public function create($data);

    /**
     * @param int $id
     * @return AggWarehouse|null
     */
    public function getById($id);

    /**
     * @param int $odooId
     * @return AggWarehouse|null
     */
    public function getByOdooId($odooId);

}