<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Agg;

use Praxigento\Warehouse\Data\Agg\Warehouse as WrhsWarehouse;

/**
 * Aggregate for warehouse with Odoo related attributes.
 */
class Warehouse extends WrhsWarehouse
{
    const AS_ODOO_ID = 'OdooId';

    public function getOdooId()
    {
        $result = parent::get(self::AS_ODOO_ID);
        return $result;
    }

    public function setOdooId($data)
    {
        parent::set(self::AS_ODOO_ID, $data);
    }

}