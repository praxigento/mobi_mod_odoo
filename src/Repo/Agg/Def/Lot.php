<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Def;

use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Data\Entity\Lot as EntityLot;
use Praxigento\Warehouse\Data\Entity\Lot as EntityWrhsLot;

class Lot
    extends \Praxigento\Core\Repo\Def\BaseCrud
    implements \Praxigento\Odoo\Repo\Agg\ILot
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var  Lot\SelectFactory */
    protected $_factorySelect;
    /** @var  bool 'true' if we know that NULL LOT is creatd */
    protected $_isNullLotExist;
    /** @var  \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var  \Praxigento\Core\Repo\Transaction\IManager */
    protected $_manTrans;
    /** @var \Praxigento\Odoo\Repo\Entity\ILot */
    protected $_repoEntityLot;
    /** @var  \Praxigento\Warehouse\Repo\Entity\ILot */
    protected $_repoWrhsEntityLot;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\Transaction\IManager $manTrans,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Warehouse\Repo\Entity\ILot $repoWrhsEntityLot,
        \Praxigento\Odoo\Repo\Entity\ILot $repoEntityLot,
        Lot\SelectFactory $factorySelect
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoWrhsEntityLot = $repoWrhsEntityLot;
        $this->_repoEntityLot = $repoEntityLot;
        $this->_factorySelect = $factorySelect;
        /* init reference */
        $this->_refDataObject = new EntityLot();
    }

    /**
     * Check that dedicated lot for Odoo products w/o lots exists (create if does not exist).
     * @return AggLot
     */
    protected function _checkNullLot()
    {
        if (!$this->_isNullLotExist) {
            $data = $this->_repoEntityLot->getByOdooId(AggLot::NULL_LOT_ID);
            if (!$data) {
                /* create NULL LOT */
                $data = new AggLot();
                $data->setCode(AggLot::NULL_LOT_CODE);
                $data->setOdooId(AggLot::NULL_LOT_ID);
                $this->create($data); // just create
            }
            $this->_isNullLotExist = true;
        }
    }

    /** @inheritdoc */
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

    /** @inheritdoc */
    public function getById($id)
    {
        $result = null;
        $query = $this->_factorySelect->getSelectQuery();
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

    /** @inheritdoc */
    public function getByOdooId($id)
    {
        $result = null;
        if (is_null($id)) {
            $this->_checkNullLot();
        }
        $query = $this->_factorySelect->getSelectQuery();
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

    /** @inheritdoc */
    public function getMageIdByOdooId($id)
    {
        if (is_null($id)) {
            $this->_checkNullLot();
        }
        $result = $this->_repoEntityLot->getMageIdByOdooId($id);
        return $result;
    }

    /** @inheritdoc */
    public function getQueryToSelect()
    {
        $result = $this->_factorySelect->getSelectQuery();
        return $result;

    }

    /** @inheritdoc */
    public function getQueryToSelectCount()
    {
        $result = $this->_factorySelect->getSelectCountQuery();
        return $result;
    }
}