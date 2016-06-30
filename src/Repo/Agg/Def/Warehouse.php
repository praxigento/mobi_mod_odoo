<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Def;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\Def\Aggregate as BaseAggRepo;
use Praxigento\Core\Repo\Transaction\IManager;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Odoo\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Odoo\Repo\Agg\IWarehouse;
use Praxigento\Odoo\Repo\Entity\IWarehouse as RepoEntityWarehouse;
use Praxigento\Warehouse\Repo\Agg\Def\Warehouse as WrhsRepoAggWarehouse;

class Warehouse extends BaseAggRepo implements IWarehouse
{

    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var  Warehouse\SelectFactory */
    protected $_factorySelect;
    /** @var  ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\Transaction\IManager */
    protected $_manTrans;
    /** @var  \Praxigento\Odoo\Repo\Entity\IWarehouse */
    protected $_repoEntityWarehouse;
    /** @var  WrhsRepoAggWarehouse */
    protected $_repoWrhsAggWarehouse;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        ObjectManagerInterface $manObj,
        IManager $manTrans,
        ResourceConnection $resource,
        WrhsRepoAggWarehouse $repoWrhsAggWarehouse,
        RepoEntityWarehouse $repoEntityWarehouse,
        Warehouse\SelectFactory $factorySelect
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoWrhsAggWarehouse = $repoWrhsAggWarehouse;
        $this->_repoEntityWarehouse = $repoEntityWarehouse;
        $this->_factorySelect = $factorySelect;
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $trans = $this->_manTrans->transactionBegin();
        try {
            $wrhsData = $this->_repoWrhsAggWarehouse->create($data);
            /* create odoo related entries */
            $bind = [
                EntityWarehouse::ATTR_MAGE_REF => $wrhsData->getId(),
                EntityWarehouse::ATTR_ODOO_REF => $data->getOdooId()
            ];
            $this->_repoEntityWarehouse->create($bind);
            $this->_manTrans->transactionCommit($trans);
            /* compose result from warehouse module's data and odoo module's data */
            $result = $this->_manObj->create(AggWarehouse::class);
            $result->setData($wrhsData);
            $result->setOdooId($data->getOdooId());
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $result = null;
        $query = $this->_factorySelect->getSelectQuery();
        $query->where(WrhsRepoAggWarehouse::AS_STOCK . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:id');
        $data = $this->_conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = $this->_manObj->create(AggWarehouse::class);
            $result->setData($data);
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getByOdooId($odooId)
    {
        /** @var  $result AggWarehouse */
        $result = null;
        $query = $this->_factorySelect->getSelectQuery();
        $query->where(static::AS_ODOO . '.' . EntityWarehouse::ATTR_ODOO_REF . '=:id');
        $data = $this->_conn->fetchRow($query, ['id' => $odooId]);
        if ($data) {
            $result = $this->_manObj->create(AggWarehouse::class);
            $result->setData($data);
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getQueryToSelect()
    {
        $result = $this->_factorySelect->getSelectQuery();
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->_factorySelect->getSelectCountQuery();
        return $result;
    }

    public function updateById($id, $data)
    {
        $trans = $this->_manTrans->transactionBegin();
        try {
            $bind = [EntityWarehouse::ATTR_ODOO_REF => $data->getData(AggWarehouse::AS_ODOO_ID)];
            $this->_repoEntityWarehouse->updateById($id, $bind);
            $this->_repoWrhsAggWarehouse->updateById($id, $data);
            $this->_manTrans->transactionCommit($trans);
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
    }
}