<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Lib\Repo\Entity\Def;

use Praxigento\Core\Lib\Context as Ctx;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Odoo\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Odoo\Lib\Repo\Entity\IWarehouse;
use Praxigento\Warehouse\Lib\Repo\Entity\Def\Warehouse as WrhsRepoWarehouse;

class Warehouse extends WrhsRepoWarehouse implements IWarehouse
{
    const AS_ODOO = 'pow';

    protected function _initQueryRead()
    {
        $result = parent::_initQueryRead();
        $dba = $this->_repoBasic->getDba();
        /* aliases and tables */
        $asStock = self::AS_STOCK;
        $asOdoo = self::AS_ODOO;
        $tblOdoo = [$asOdoo => $dba->getTableName(EntityWarehouse::ENTITY_NAME)];
        /* LEFT LOIN prxgt_odoo_wrhs */
        $cols = [
            AggWarehouse::AS_ODOO_ID => EntityWarehouse::ATTR_ODOO_ID,
            AggWarehouse::AS_CURRENCY => EntityWarehouse::ATTR_CURRENCY,
        ];
        $on = $asOdoo . '.' . EntityWarehouse::ATTR_MAGE_ID . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
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
        $dba = $this->_repoBasic->getDba();
        $manTrans = $dba->getTransactionManager();
        $trans = $manTrans->transactionBegin();
        try {
            $result = parent::create($data);
            /* create odoo related entries */
            $tbl = EntityWarehouse::ENTITY_NAME;
            $bind = [
                EntityWarehouse::ATTR_MAGE_ID => $result->getId(),
                EntityWarehouse::ATTR_ODOO_ID => $data->getOdooId(),
                EntityWarehouse::ATTR_CURRENCY => $data->getCurrency()
            ];
            $this->_repoBasic->addEntity($tbl, $bind);
            $manTrans->transactionCommit($trans);
        } finally {
            $manTrans->transactionClose($trans);
        }
        return $result;
    }

    public function getByOdooId($odooId)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $query = $this->_initQueryRead();
        $query->where(self::AS_ODOO . '.' . EntityWarehouse::ATTR_ODOO_ID . '=:id');
        $sql = (string)$query;
        $dba = $this->_repoBasic->getDba();
        $conn = $dba->getDefaultConnection();
        $data = $conn->fetchRow($query, ['id' => $odooId]);
        if ($data) {
            $result = $this->_initResultRead($data);
        }
        return $result;
    }
}