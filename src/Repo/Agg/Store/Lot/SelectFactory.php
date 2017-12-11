<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Store\Lot;

use Praxigento\Odoo\Repo\Agg\Data\Lot as AggLot;
use Praxigento\Odoo\Repo\Agg\Store\ILot;
use Praxigento\Odoo\Repo\Entity\Data\Lot as EntityLot;
use Praxigento\Warehouse\Repo\Entity\Data\Lot as EntityWrhsLot;

/**
 * Compose SELECT query to get Lot aggregate.
 */
class SelectFactory
    extends \Praxigento\Core\App\Repo\Agg\BaseSelectFactory
{

    public function getQueryToSelect()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asWrhs = ILot::AS_WRHS;
        $asOdoo = ILot::AS_ODOO;
        $tblWrhs = [$asWrhs => $this->_resource->getTableName(EntityWrhsLot::ENTITY_NAME)];
        $tblOdoo = [$asOdoo => $this->_resource->getTableName(EntityLot::ENTITY_NAME)];
        /* SELECT FROM prxgt_wrhs_lot */
        $cols = [
            AggLot::AS_ID => EntityWrhsLot::ATTR_ID,
            AggLot::AS_CODE => EntityWrhsLot::ATTR_CODE,
            AggLot::AS_EXP_DATE => EntityWrhsLot::ATTR_EXP_DATE
        ];
        $result->from($tblWrhs, $cols);
        /* LEFT JOIN prxgt_odoo_lot */
        $cols = [
            AggLot::AS_ODOO_ID => EntityLot::ATTR_ODOO_REF
        ];
        $cond = $asOdoo . '.' . EntityLot::ATTR_MAGE_REF . '=' . $asWrhs . '.' . EntityWrhsLot::ATTR_ID;
        $result->joinLeft($tblOdoo, $cond, $cols);
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asWrhs = ILot::AS_WRHS;
        $asOdoo = ILot::AS_ODOO;
        $tblWrhs = [$asWrhs => $this->_resource->getTableName(EntityWrhsLot::ENTITY_NAME)];
        $tblOdoo = [$asOdoo => $this->_resource->getTableName(EntityLot::ENTITY_NAME)];
        /* SELECT FROM prxgt_wrhs_lot */
        $cols = "COUNT(" . EntityWrhsLot::ATTR_ID . ")";
        $result->from($tblWrhs, $cols);
        /* LEFT JOIN prxgt_odoo_lot */
        $cols = [];
        $cond = $asOdoo . '.' . EntityLot::ATTR_MAGE_REF . '=' . $asWrhs . '.' . EntityWrhsLot::ATTR_ID;
        $result->joinLeft($tblOdoo, $cond, $cols);
        return $result;
    }
}