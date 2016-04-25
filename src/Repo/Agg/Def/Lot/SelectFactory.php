<?php
/**
 * Compose SELECT query to get Lot aggregate.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Def\Lot;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\IHasSelectQuery;
use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Data\Entity\Lot as EntityLot;
use Praxigento\Odoo\Repo\Agg\ILot;
use Praxigento\Warehouse\Data\Entity\Lot as EntityWrhsLot;

class SelectFactory implements IHasSelectQuery
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
    }

    /**
     * @inheritdoc
     */
    public function getSelectQuery()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asWrhs = ILot::AS_WRHS;
        $asOdoo = ILot::AS_ODOO;
        $tblWrhs = [$asWrhs => $this->_conn->getTableName(EntityWrhsLot::ENTITY_NAME)];
        $tblOdoo = [$asOdoo => $this->_conn->getTableName(EntityLot::ENTITY_NAME)];
        /* SELECT FROM prxgt_wrhs_lot */
        $cols = [
            AggLot::AS_ID => EntityWrhsLot::ATTR_ID,
            AggLot::AS_CODE => EntityWrhsLot::ATTR_CODE,
            AggLot::AS_EXP_DATE => EntityWrhsLot::ATTR_EXP_DATE
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
}