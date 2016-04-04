<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Agg;

use Praxigento\Warehouse\Data\Agg\Warehouse as WrhsWarehouse;


class Warehouse extends WrhsWarehouse
{
    const AS_CURRENCY = 'Currency';
    const AS_ODOO_ID = 'OdooId';


    public function getCurrency()
    {
        $result = parent::getData(self::AS_CURRENCY);
        return $result;
    }

    public function getOdooId()
    {
        $result = parent::getData(self::AS_ODOO_ID);
        return $result;
    }

    public function setCurrency($data)
    {
        parent::setData(self::AS_CURRENCY, $data);
    }

    public function setOdooId($data)
    {
        parent::setData(self::AS_ODOO_ID, $data);
    }

}