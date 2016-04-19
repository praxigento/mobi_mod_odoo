<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Def\Sub;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Odoo\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Odoo\Repo\Agg\IWarehouse;
use Praxigento\Warehouse\Repo\Agg\Def\Warehouse as WrhsRepoAggWarehouse;

class Select implements \Praxigento\Core\Repo\IHasQuery
{
    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var  WrhsRepoAggWarehouse */
    protected $_repoWrhsWarehouse;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        ResourceConnection $resource,
        WrhsRepoAggWarehouse $repoWrhsWarehouse
    ) {
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
        $this->_repoWrhsWarehouse = $repoWrhsWarehouse;
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        $result = $this->_repoWrhsWarehouse->getQueryToSelect();
        /* aliases and tables */
        $asStock = WrhsRepoAggWarehouse::AS_STOCK;
        $asOdoo = IWarehouse::AS_ODOO;
        $tblOdoo = [$asOdoo => $this->_conn->getTableName(EntityWarehouse::ENTITY_NAME)];
        /* LEFT JOIN prxgt_odoo_wrhs */
        $cols = [
            AggWarehouse::AS_ODOO_ID => EntityWarehouse::ATTR_ODOO_REF
        ];
        $on = $asOdoo . '.' . EntityWarehouse::ATTR_MAGE_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $result->joinLeft($tblOdoo, $on, $cols);
        return $result;
    }
}