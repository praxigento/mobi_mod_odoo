<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Plugin\Framework\View\Element\UiComponent\DataProvider\Sub;

use Praxigento\Core\Repo\Query\Expression;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Entity\SaleOrder;

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
        $fieldFullName = self::AS_ODOO_SALE . '.' . SaleOrder::ATTR_MAGE_REF;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
        /* MOBI-718: applied_rule_ids */
        $fieldAlias = Cfg::E_SALE_ORDER_A_APPLIED_RULE_IDS;
        $fieldFullName = self::AS_SALE_ORDER . '.' . Cfg::E_SALE_ORDER_A_APPLIED_RULE_IDS;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);

    }

    public function populateSelect(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
    ) {
        $select = $collection->getSelect();
        /* LEFT JOIN `prxgt_odoo_sale` */
        $tbl = [self::AS_ODOO_SALE => $this->_resource->getTableName(SaleOrder::ENTITY_NAME)];
        $on = self::AS_ODOO_SALE . '.' . SaleOrder::ATTR_MAGE_REF . '=main_table.' . Cfg::E_SALE_ORDER_A_ENTITY_ID;
        $exp = new Expression('!ISNULL(' . self::AS_ODOO_SALE . '.' . SaleOrder::ATTR_MAGE_REF . ')');
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