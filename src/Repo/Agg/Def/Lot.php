<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Def;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\IBasic as IBasicRepo;
use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Data\Entity\Lot as EntityLot;
use Praxigento\Odoo\Repo\Agg\ILot;
use Praxigento\Warehouse\Data\Entity\Lot as EntityWrhsLot;

class Lot implements ILot
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var  ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\ITransactionManager */
    protected $_manTrans;
    /** @var IBasicRepo */
    protected $_repoBasic;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;
    /** @var  Sub\Select */
    protected $_subSelect;

    public function __construct(
        ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Magento\Framework\App\ResourceConnection $resource,
        IBasicRepo $repoBasic,
        Lot\Select $subSelect
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoBasic = $repoBasic;
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
            $tbl = EntityWrhsLot::ENTITY_NAME;
            $bind = [
                EntityWrhsLot::ATTR_CODE => $data->getCode(),
                EntityWrhsLot::ATTR_EXP_DATE => $data->getExpDate()

            ];
            $id = $this->_repoBasic->addEntity($tbl, $bind);
            /* register lot in Odoo module */
            $tbl = EntityLot::ENTITY_NAME;
            $bind = [
                EntityLot::ATTR_MAGE_REF => $id,
                EntityLot::ATTR_ODOO_REF => $data->getOdooId()
            ];
            $this->_repoBasic->addEntity($tbl, $bind);
            $this->_manTrans->transactionCommit($trans);
            /* compose result from warehouse module's data and odoo module's data */
            $result = $this->_manObj->create(AggLot::class);
            $result->setData($data);
            $result->setId($id);
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
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