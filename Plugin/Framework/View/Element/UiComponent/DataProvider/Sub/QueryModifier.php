<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Plugin\Framework\View\Element\UiComponent\DataProvider\Sub;

use Praxigento\Core\App\Repo\Query\Expression;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Repo\Data\SaleOrder;

/**
 * TODO: this plugin is not plugged. Remove it or plug.
 */
class QueryModifier
{


    /* Tables aliases */
    const AS_ODOO_SALE = 'prxgtOdooSales';
    const AS_SALE_ORDER = 'saleOrder';

    /* Columns aliases */
    const A_IS_IN_ODOO = 'prxgt_is_in_odoo';


    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_resource = $resource;
    }

    public function addFieldsMapping(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
    ) {
        // is_in_odoo
        $fieldAlias = self::A_IS_IN_ODOO;
        $fieldFullName = self::AS_ODOO_SALE . '.' . SaleOrder::A_MAGE_REF;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* MOBI-718: applied_rule_ids */
        $fieldAlias = Cfg::E_SALE_ORDER_A_APPLIED_RULE_IDS;
        $fieldFullName = self::AS_SALE_ORDER . '.' . Cfg::E_SALE_ORDER_A_APPLIED_RULE_IDS;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /**
         * MOBI-1531: map 'main_table' fields used in grid filter
         */
        /* created_at / Purchase Date*/
        $fieldAlias = Cfg::E_SALE_ORDER_GRID_A_CREATED_AT;
        $fieldFullName = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_GRID_A_CREATED_AT;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* base_grand_total / Grand Total (Base) */
        $fieldAlias = Cfg::E_SALE_ORDER_GRID_A_BASE_GRAND_TOTAL;
        $fieldFullName = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_GRID_A_BASE_GRAND_TOTAL;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* grand_total / Grand Total (Purchased) */
        $fieldAlias = Cfg::E_SALE_ORDER_GRID_A_GRAND_TOTAL;
        $fieldFullName = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_GRID_A_GRAND_TOTAL;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* store_id / Purchase Point */
        $fieldAlias = Cfg::E_SALE_ORDER_GRID_A_STORE_ID;
        $fieldFullName = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_GRID_A_STORE_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* increment_id / ID */
        $fieldAlias = Cfg::E_SALE_ORDER_GRID_A_INCREMENT_ID;
        $fieldFullName = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_GRID_A_INCREMENT_ID;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* billing_name / Bill-to Name */
        $fieldAlias = Cfg::E_SALE_ORDER_GRID_A_BILLING_NAME;
        $fieldFullName = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_GRID_A_BILLING_NAME;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* shipping_name / Ship-to Name */
        $fieldAlias = Cfg::E_SALE_ORDER_GRID_A_SHIPPING_NAME;
        $fieldFullName = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_GRID_A_SHIPPING_NAME;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* status / Status */
        $fieldAlias = Cfg::E_SALE_ORDER_GRID_A_STATUS;
        $fieldFullName = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_GRID_A_STATUS;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* signifyd_guarantee_status / Signifyd Guarantee Decision */
        $fieldAlias = Cfg::E_SALE_ORDER_GRID_A_SHIPPING_NAME;
        $fieldFullName = Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_GRID_A_SHIPPING_NAME;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
    }

    public function populateSelect(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
    ) {
        $select = $collection->getSelect();
        /* LEFT JOIN `prxgt_odoo_sale` */
        $tbl = [self::AS_ODOO_SALE => $this->_resource->getTableName(SaleOrder::ENTITY_NAME)];
        $on = self::AS_ODOO_SALE . '.' . SaleOrder::A_MAGE_REF . '=main_table.' . Cfg::E_SALE_ORDER_A_ENTITY_ID;
        $exp = new Expression('!ISNULL(' . self::AS_ODOO_SALE . '.' . SaleOrder::A_MAGE_REF . ')');
        $cols = [
            self::A_IS_IN_ODOO => $exp
        ];
        $select->joinLeft($tbl, $on, $cols);

        /* MOBI-718: dumb realization of the feature */
        $tbl = $this->_resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER);
        $as = self::AS_SALE_ORDER;
        $on = $as . '.' . Cfg::E_SALE_ORDER_A_ENTITY_ID . '=' . Cfg::AS_MAIN_TABLE . '.' . Cfg::E_SALE_ORDER_A_ENTITY_ID;
        $cols = [Cfg::E_SALE_ORDER_A_APPLIED_RULE_IDS];
        $select->joinLeft([$as => $tbl], $on, $cols);

        return $select;
    }

}