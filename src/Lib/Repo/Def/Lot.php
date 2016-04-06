<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Lib\Repo\Def;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Repo\IBasic as IBasicRepo;
use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Data\Entity\Lot as EntityLot;
use Praxigento\Odoo\Lib\Repo\ILot;
use Praxigento\Warehouse\Data\Entity\Lot as EntityWrhsLot;

class Lot implements ILot
{
    /**#@+
     *  Aliases for tables in DB.
     */
    const AS_ODOO = 'pol';
    const AS_WRHS = 'pwl';
    /**#@-*/
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

    public function __construct(
        ObjectManagerInterface $manObj,
        \Praxigento\Core\Repo\ITransactionManager $manTrans,
        \Magento\Framework\App\ResourceConnection $resource,
        IBasicRepo $repoBasic
    ) {
        $this->_manObj = $manObj;
        $this->_manTrans = $manTrans;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoBasic = $repoBasic;
    }

    protected function _initQueryRead()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asWrhs = self::AS_WRHS;
        $asOdoo = self::AS_ODOO;
        $tblWrhs = [$asWrhs => $this->_conn->getTableName(EntityWrhsLot::ENTITY_NAME)];
        $tblOdoo = [$asOdoo => $this->_conn->getTableName(EntityLot::ENTITY_NAME)];
        /* SELECT FROM prxgt_wrhs_lot */
        $cols = [
            AggLot::ATTR_ID => EntityWrhsLot::ATTR_ID,
            AggLot::ATTR_CODE => EntityWrhsLot::ATTR_CODE,
            AggLot::ATTR_EXP_DATE => EntityWrhsLot::ATTR_EXP_DATE
        ];
        $result->from($tblWrhs, $cols);
        /* LEFT JOIN prxgt_odoo_lot */
        $cols = [
            AggLot::AS_ODOO_ID => EntityLot::ATTR_ODOO_REF
        ];
        $on = $asOdoo . '.' . EntityLot::ATTR_MAGE_REF . '=' . $asWrhs . '.' . EntityWrhsLot::ATTR_ID;
        $result->joinLeft($tblOdoo, $on, $cols);
        return $result;
    }

    /**
     * @param AggLot $data
     */
    protected function _register($data)
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
        } finally {
            $this->_manTrans->transactionClose($trans);
        }
    }

    public function checkExistence(AggLot $data)
    {
        $odooRef = $data->getOdooId();
        $found = $this->getByOdooId($odooRef);
        if (!$found) {
            // register new Lot and re-select data after registration
            $this->_register($data);
            $found = $this->getByOdooId($odooRef);
        }
        $result = ($found) ? $found : null;
        return $result;
    }

    public function getById($id)
    {
        $result = null;
        $query = $this->_initQueryRead();
        $where = self::AS_WRHS . '.' . EntityWrhsLot::ATTR_ID . '=:id';
        $query->where($where);
        $conn = $this->_conn->getDefaultConnection();
        $data = $conn->fetchRow($query, ['id' => $id]);
        if ($data) {
            /** @var  $result AggLot */
            $result = $this->_manObj->create(AggLot::class);
            $result->setData($data);
        }
        return $result;
    }

    public function getByOdooId($id)
    {
        $result = null;
        $query = $this->_initQueryRead();
        $where = self::AS_ODOO . '.' . EntityLot::ATTR_ODOO_REF . '=:id';
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