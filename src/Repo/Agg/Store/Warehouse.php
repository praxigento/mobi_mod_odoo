<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Store;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Transaction\Database\IManager;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Repo\Agg\Data\Warehouse as AggWarehouse;
use Praxigento\Odoo\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Odoo\Repo\Entity\IWarehouse as RepoEntityWarehouse;
use Praxigento\Warehouse\Repo\Agg\Def\Warehouse as WrhsRepoAggWarehouse;

class Warehouse
    extends \Praxigento\Core\Repo\Def\Crud
    implements \Praxigento\Odoo\Repo\Agg\Store\IWarehouse
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var  Warehouse\SelectFactory */
    protected $factorySelect;
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $manTrans;
    /** @var  \Praxigento\Odoo\Repo\Entity\IWarehouse */
    protected $repoEntityWarehouse;
    /** @var  WrhsRepoAggWarehouse */
    protected $repoWrhsAggWarehouse;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        IManager $manTrans,
        ResourceConnection $resource,
        WrhsRepoAggWarehouse $repoWrhsAggWarehouse,
        RepoEntityWarehouse $repoEntityWarehouse,
        Warehouse\SelectFactory $factorySelect
    ) {
        $this->manTrans = $manTrans;
        $this->resource = $resource;
        $this->conn = $resource->getConnection();
        $this->repoWrhsAggWarehouse = $repoWrhsAggWarehouse;
        $this->repoEntityWarehouse = $repoEntityWarehouse;
        $this->factorySelect = $factorySelect;
    }

    public function create($data)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $def = $this->manTrans->begin();
        try {
            $wrhsData = $this->repoWrhsAggWarehouse->create($data);
            /* create odoo related entries */
            $bind = [
                EntityWarehouse::ATTR_MAGE_REF => $wrhsData->getId(),
                EntityWarehouse::ATTR_ODOO_REF => $data->getOdooId()
            ];
            $this->repoEntityWarehouse->create($bind);
            $this->manTrans->commit($def);
            /* compose result from warehouse module's data and odoo module's data */
            $result = new \Praxigento\Odoo\Repo\Agg\Data\Warehouse();
            $result->set($wrhsData);
            $result->setOdooId($data->getOdooId());
        } finally {
            $this->manTrans->end($def);
        }
        return $result;
    }

    public function getById($id)
    {
        $result = null;
        $query = $this->factorySelect->getQueryToSelect();
        $query->where(WrhsRepoAggWarehouse::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:id');
        $data = $this->conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = new \Praxigento\Odoo\Repo\Agg\Data\Warehouse();
            $result->set($data);
        }
        return $result;
    }

    public function getByOdooId($odooId)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $query = $this->factorySelect->getQueryToSelect();
        $query->where(static::AS_ODOO . '.' . EntityWarehouse::ATTR_ODOO_REF . '=:id');
        $data = $this->conn->fetchRow($query, ['id' => $odooId]);
        if ($data) {
            $result = new \Praxigento\Odoo\Repo\Agg\Data\Warehouse();
            $result->set($data);
        }
        return $result;
    }

    public function getQueryToSelect()
    {
        $result = $this->factorySelect->getQueryToSelect();
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->factorySelect->getQueryToSelectCount();
        return $result;
    }

    /**
     * @param array|int|string $id
     * @param array|\Flancer32\Lib\Data $data
     * @return null
     */
    public function updateById($id, $data)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $def = $this->manTrans->begin();
        try {
            $this->repoWrhsAggWarehouse->updateById($id, $data);
            /* update odoo related entries */
            $bind = [
                EntityWarehouse::ATTR_MAGE_REF => $data->getId(),
                EntityWarehouse::ATTR_ODOO_REF => $data->getOdooId()
            ];
            $this->repoEntityWarehouse->updateById($id, $bind);
            $this->manTrans->commit($def);
        } finally {
            $this->manTrans->end($def);
        }
    }


}