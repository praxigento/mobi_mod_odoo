<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Def;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\Def\Aggregate as BaseAggRepo;
use Praxigento\Core\Repo\ITransactionManager;
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
    /** @var  ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var  \Praxigento\Odoo\Repo\Entity\IWarehouse */
    protected $_repoEntityWarehouse;
    /** @var  WrhsRepoAggWarehouse */
    protected $_repoWrhsAggWarehouse;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;
    /** @var  Warehouse\Select */
    protected $_subSelect;

    public function __construct(
        ObjectManagerInterface $manObj,
        ITransactionManager $manTrans,
        ResourceConnection $resource,
        WrhsRepoAggWarehouse $repoWrhsAggWarehouse,
        RepoEntityWarehouse $repoEntityWarehouse,
        Warehouse\Select $subSelect
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoWrhsAggWarehouse = $repoWrhsAggWarehouse;
        $this->_repoEntityWarehouse = $repoEntityWarehouse;
        $this->_subSelect = $subSelect;
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
        $query = $this->_subSelect->getSelectQuery();
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
        $query = $this->_subSelect->getSelectQuery();
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
        $result = $this->_subSelect->getSelectQuery();
        return $result;
    }
}