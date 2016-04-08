<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Def;

use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Odoo\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Odoo\Repo\Agg\IWarehouse;
use Praxigento\Warehouse\Repo\Agg\Def\Warehouse as WrhsRepoWarehouse;

class Warehouse extends WrhsRepoWarehouse implements IWarehouse
{
    const AS_ODOO = 'pow';

    protected function _initQueryRead()
    {
        $result = parent::_initQueryRead();
        /* aliases and tables */
        $asStock = self::AS_STOCK;
        $asOdoo = self::AS_ODOO;
        $tblOdoo = [$asOdoo => $this->_conn->getTableName(EntityWarehouse::ENTITY_NAME)];
        /* LEFT LOIN prxgt_odoo_wrhs */
        $cols = [
            AggWarehouse::AS_ODOO_ID => EntityWarehouse::ATTR_ODOO_REF
        ];
        $on = $asOdoo . '.' . EntityWarehouse::ATTR_MAGE_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $result->joinLeft($tblOdoo, $on, $cols);
        return $result;
    }

    protected function _initResultRead($data)
    {
        /** @var  $result AggWarehouse */
        $result = $this->_manObj->create(AggWarehouse::class);
        $result->setData($data);
        return $result;
    }

    public function create($data)
    {
        $trans = $this->_manTrans->transactionBegin();
        try {
            $result = parent::create($data);
            /* create odoo related entries */
            $tbl = EntityWarehouse::ENTITY_NAME;
            $bind = [
                EntityWarehouse::ATTR_MAGE_REF => $result->getId(),
                EntityWarehouse::ATTR_ODOO_REF => $data->getOdooId()
            ];
            $this->_repoBasic->addEntity($tbl, $bind);
            $this->_manTrans->transactionCommit($trans);
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }

    public function getByOdooId($odooId)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $query = $this->_initQueryRead();
        $query->where(self::AS_ODOO . '.' . EntityWarehouse::ATTR_ODOO_REF . '=:id');
        $data = $this->_conn->fetchRow($query, ['id' => $odooId]);
        if ($data) {
            $result = $this->_initResultRead($data);
        }
        return $result;
    }
}