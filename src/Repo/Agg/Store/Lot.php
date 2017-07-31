<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Store;

use Praxigento\Odoo\Data\Entity\Lot as EntityLot;
use Praxigento\Odoo\Repo\Agg\Data\Lot as AggLot;
use Praxigento\Warehouse\Data\Entity\Lot as EntityWrhsLot;

class Lot
    extends \Praxigento\Core\Repo\Def\Crud
    implements \Praxigento\Odoo\Repo\Agg\Store\ILot
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $conn;
    /** @var  Lot\SelectFactory */
    protected $factorySelect;
    /** @var  bool 'true' if we know that NULL LOT is creatd */
    protected $isNullLotExist;
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $manTrans;
    /** @var \Praxigento\Odoo\Repo\Entity\Lot */
    protected $repoEntityLot;
    /** @var  \Praxigento\Warehouse\Repo\Entity\Def\Lot */
    protected $repoWrhsEntityLot;

    public function __construct(
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Warehouse\Repo\Entity\Def\Lot $repoWrhsEntityLot,
        \Praxigento\Odoo\Repo\Entity\Lot $repoEntityLot,
        Lot\SelectFactory $factorySelect
    ) {
        $this->manTrans = $manTrans;
        $this->conn = $resource->getConnection();
        $this->repoWrhsEntityLot = $repoWrhsEntityLot;
        $this->repoEntityLot = $repoEntityLot;
        $this->factorySelect = $factorySelect;
    }

    /**
     * Check that dedicated lot for Odoo products w/o lots exists (create if does not exist).
     * @return AggLot
     */
    public function _checkNullLot()
    {
        if (!$this->isNullLotExist) {
            $data = $this->repoEntityLot->getByOdooId(AggLot::NULL_LOT_ID);
            if (!$data) {
                /* create NULL LOT */
                $data = new AggLot();
                $data->setCode(AggLot::NULL_LOT_CODE);
                $data->setOdooId(AggLot::NULL_LOT_ID);
                $this->create($data); // just create
            }
            $this->isNullLotExist = true;
        }
    }

    public function create($data)
    {
        $def = $this->manTrans->begin();
        try {
            /* register lot in Warehouse module */
            $bind = [
                EntityWrhsLot::ATTR_CODE => $data->getCode(),
                EntityWrhsLot::ATTR_EXP_DATE => $data->getExpDate()

            ];
            $id = $this->repoWrhsEntityLot->create($bind);
            /* register lot in Odoo module */
            $bind = [
                EntityLot::ATTR_MAGE_REF => $id,
                EntityLot::ATTR_ODOO_REF => $data->getOdooId()
            ];
            $this->repoEntityLot->create($bind);
            $this->manTrans->commit($def);
            /* compose result from warehouse module's data and odoo module's data */
            $result = new \Praxigento\Odoo\Repo\Agg\Data\Lot();
            $result->set($data);
            $result->setId($id);
        } finally {
            $this->manTrans->end($def);
        }
        return $result;
    }

    public function getById($id)
    {
        $result = null;
        $query = $this->factorySelect->getQueryToSelect();
        $where = static::AS_WRHS . '.' . EntityWrhsLot::ATTR_ID . '=:id';
        $query->where($where);
        $data = $this->conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = new \Praxigento\Odoo\Repo\Agg\Data\Lot();
            $result->set($data);
        }
        return $result;
    }

    public function getByOdooId($id)
    {
        $result = null;
        if (is_null($id)) {
            $this->_checkNullLot();
        }
        $query = $this->factorySelect->getQueryToSelect();
        $where = static::AS_ODOO . '.' . EntityLot::ATTR_ODOO_REF . '=:id';
        $query->where($where);
        $data = $this->conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            $result = new \Praxigento\Odoo\Repo\Agg\Data\Lot();
            $result->set($data);
        }
        return $result;
    }

    public function getMageIdByOdooId($id)
    {
        if (is_null($id)) {
            $this->_checkNullLot();
        }
        $result = $this->repoEntityLot->getMageIdByOdooId($id);
        return $result;
    }

    public function getOdooIdByMageId($id)
    {
        $result = $this->repoEntityLot->getOdooIdByMageId($id);
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
}