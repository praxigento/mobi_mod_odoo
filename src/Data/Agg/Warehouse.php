<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Agg;

use Praxigento\Warehouse\Data\Agg\Warehouse as WrhsWarehouse;


class Warehouse extends WrhsWarehouse
{
    const AS_ODOO_ID = 'OdooId';

    public function getOdooId()
    {
        $result = parent::getData(self::AS_ODOO_ID);
        return $result;
    }

    public function setOdooId($data)
    {
        parent::setData(self::AS_ODOO_ID, $data);
    }

}