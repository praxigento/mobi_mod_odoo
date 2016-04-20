<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Def;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Data\Entity\Lot as EntityLot;
use Praxigento\Odoo\Repo\Agg\ILot;
use Praxigento\Odoo\Repo\Entity\ILot as IRepoEntityLot;
use Praxigento\Warehouse\Data\Entity\Lot as EntityWrhsLot;
use Praxigento\Warehouse\Repo\Entity\ILot as IRepoWrhsEntityLot;

class Lot implements ILot
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var  ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var IRepoEntityLot */
    protected $_repoEntityLot;
    /** @var  IRepoWrhsEntityLot */
    protected $_repoWrhsEntityLot;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;
    /** @var  Lot\Select */
    protected $_subSelect;

    public function __construct(
        ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Magento\Framework\App\ResourceConnection $resource,
        IRepoWrhsEntityLot $repoWrhsEntityLot,
        IRepoEntityLot $repoEntityLot,
        Lot\Select $subSelect
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoWrhsEntityLot = $repoWrhsEntityLot;
        $this->_repoEntityLot = $repoEntityLot;
        $this->_subSelect = $subSelect;
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $trans = $this->_manTrans->transactionBegin();
        try {
            /* register lot in Warehouse module */
            $bind = [
                EntityWrhsLot::ATTR_CODE => $data->getCode(),
                EntityWrhsLot::ATTR_EXP_DATE => $data->getExpDate()

            ];
            $id = $this->_repoWrhsEntityLot->create($bind);
            /* register lot in Odoo module */
            $bind = [
                EntityLot::ATTR_MAGE_REF => $id,
                EntityLot::ATTR_ODOO_REF => $data->getOdooId()
            ];
            $this->_repoEntityLot->create($bind);
            $this->_manTrans->transactionCommit($trans);
            /* compose result from warehouse module's data and odoo module's data */
            $result = $this->_manObj->create(AggLot::class);
            $result->setData($data);
            $result->setId($id);
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
        $query = $this->_subSelect->getQuery();
        $where = static::AS_WRHS . '.' . EntityWrhsLot::ATTR_ID . '=:id';
        $query->where($where);
        $data = $this->_conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            /** @var  $result AggLot */
            $result = $this->_manObj->create(AggLot::class);
            $result->setData($data);
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getByOdooId($id)
    {
        $result = null;
        $query = $this->_subSelect->getQuery();
        $where = static::AS_ODOO . '.' . EntityLot::ATTR_ODOO_REF . '=:id';
        $query->where($where);
        $data = $this->_conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            /** @var  $result AggLot */
            $result = $this->_manObj->create(AggLot::class);
            $result->setData($data);
        }
        return $result;
    }
}