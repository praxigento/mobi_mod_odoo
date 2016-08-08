<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Plugin\Framework\View\Element\UiComponent\DataProvider\Sub;

use Praxigento\Core\Repo\Query\Expression;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Entity\SaleOrder;

class QueryModifier
{
    const AS_FLD_IS_IN_ODOO = 'prxgt_is_in_odoo';
    const AS_TBL_ODOO_SALE = 'prxgtOdooSales';

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
        $fieldAlias = self::AS_FLD_IS_IN_ODOO;
        $fieldFullName = self::AS_TBL_ODOO_SALE . '.' . SaleOrder::ATTR_MAGE_REF;
        $collection->addFilterToMap($fieldAlias, $fieldFullName);
    }

    public function populateSelect(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection
    ) {
        $select = $collection->getSelect();
        /* LEFT JOIN `prxgt_odoo_sale` */
        $tbl = [self::AS_TBL_ODOO_SALE => $this->_resource->getTableName(SaleOrder::ENTITY_NAME)];
        $on = self::AS_TBL_ODOO_SALE . '.' . SaleOrder::ATTR_MAGE_REF . '=main_table.' . Cfg::E_SALE_ORDER_A_ENTITY_ID;
        $exp = new Expression('!ISNULL(' . self::AS_TBL_ODOO_SALE . '.' . SaleOrder::ATTR_MAGE_REF . ')');
        $cols = [
            self::AS_FLD_IS_IN_ODOO => $exp
        ];
        $select->joinLeft($tbl, $on, $cols);
        return $select;
    }

}