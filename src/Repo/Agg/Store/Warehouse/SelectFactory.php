<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Store\Warehouse;

use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Odoo\Repo\Agg\Data\Warehouse as AggWarehouse;
use Praxigento\Odoo\Repo\Agg\Store\IWarehouse;
use Praxigento\Warehouse\Repo\Agg\Def\Warehouse as WrhsRepoAggWarehouse;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class SelectFactory
    extends \Praxigento\Core\Repo\Agg\BaseSelectFactory
{
    /** @var  WrhsRepoAggWarehouse */
    protected $_repoAggWarehouse;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Warehouse\Repo\Agg\Def\Warehouse $repoWrhsAggWarehouse
    ) {
        parent::__construct($resource);
        $this->_repoAggWarehouse = $repoWrhsAggWarehouse;
    }

    public function getQueryToSelect()
    {
        $result = $this->_repoAggWarehouse->getQueryToSelect();
        /* aliases and tables */
        $asStock = WrhsRepoAggWarehouse::AS_STOCK;
        $asOdoo = IWarehouse::AS_ODOO;
        $tblOdoo = [$asOdoo => $this->_resource->getTableName(EntityWarehouse::ENTITY_NAME)];
        /* LEFT JOIN prxgt_odoo_wrhs */
        $cols = [
            AggWarehouse::AS_ODOO_ID => EntityWarehouse::ATTR_ODOO_REF
        ];
        $cond = $asOdoo . '.' . EntityWarehouse::ATTR_MAGE_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $result->joinLeft($tblOdoo, $cond, $cols);
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->_repoAggWarehouse->getQueryToSelectCount();
        /* aliases and tables */
        $asStock = WrhsRepoAggWarehouse::AS_STOCK;
        $asOdoo = IWarehouse::AS_ODOO;
        $tblOdoo = [$asOdoo => $this->_resource->getTableName(EntityWarehouse::ENTITY_NAME)];
        /* LEFT JOIN prxgt_odoo_wrhs */
        $cols = [];
        $cond = $asOdoo . '.' . EntityWarehouse::ATTR_MAGE_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $result->joinLeft($tblOdoo, $cond, $cols);
        return $result;
    }
}