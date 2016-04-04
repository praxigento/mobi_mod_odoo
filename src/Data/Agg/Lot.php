<?php
/**
 * Aggregation for 
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Agg;

use Praxigento\Warehouse\Data\Entity\Lot as WrhsEntityLot;

class Lot extends WrhsEntityLot
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