<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Entity\Def;

use Praxigento\Core\Repo\Query\Expression;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Entity\SaleOrder as Entity;

class SaleOrder
    extends \Praxigento\Odoo\Repo\Entity\Def\BaseOdooEntity
    implements \Praxigento\Odoo\Repo\Entity\ISaleOrder
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /** @inheritdoc */
    public function getIdsToSaveToOdoo()
    {
        /* aliases and tables */
        $asSaleOrder = 'so';
        $asOdooReg = 'pos';
        $tblSaleOrder = [$asSaleOrder => $this->_resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER)];
        $tblOdooReg = [$asOdooReg => $this->_resource->getTableName(Entity::ENTITY_NAME)];
        /* SELECT FROM sales_order */
        $query = $this->_conn->select();
        $cols = [Cfg::E_SALE_ORDER_A_ENTITY_ID];
        $query->from($tblSaleOrder, $cols);
        // LEFT OUTER JOIN prxgt_odoo_sale
        $on = $asOdooReg . '.' . Entity::ATTR_MAGE_REF . '=' . $asSaleOrder . '.' . Cfg::E_SALE_ORDER_A_ENTITY_ID;
        $cols = [];
        $query->joinLeft($tblOdooReg, $on, $cols);
        /* WHERE */
        $where = new Expression('ISNULL(' . $asOdooReg. '.' . Entity::ATTR_MAGE_REF . ')');
        $query->where($where);
        /* fetch data */
        $result = $this->_conn->fetchAll($query);
        return $result;
    }
}