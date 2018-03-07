<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity;

use Praxigento\Core\App\Repo\Query\Expression;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Repo\Entity\Data\SaleOrder as Entity;

class SaleOrder
    extends \Praxigento\Odoo\Repo\Entity\BaseOdooEntity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /**
     * Get Magento IDs to save new orders into Odoo.
     *
     * @return int[] Magento IDs of the orders to be replicated.
     */
    public function getIdsToSaveToOdoo()
    {
        /* aliases and tables */
        $asSaleOrder = 'so';
        $asOdooReg = 'pos';
        $tblSaleOrder = [$asSaleOrder => $this->resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER)];
        $tblOdooReg = [$asOdooReg => $this->resource->getTableName(Entity::ENTITY_NAME)];
        /* SELECT FROM sales_order */
        $query = $this->conn->select();
        $cols = [Cfg::E_SALE_ORDER_A_ENTITY_ID];
        $query->from($tblSaleOrder, $cols);
        // LEFT OUTER JOIN prxgt_odoo_sale
        $cond = $asOdooReg . '.' . Entity::ATTR_MAGE_REF . '=' . $asSaleOrder . '.' . Cfg::E_SALE_ORDER_A_ENTITY_ID;
        $cols = [];
        $query->joinLeft($tblOdooReg, $cond, $cols);
        /* WHERE */
        $where = new Expression('ISNULL(' . $asOdooReg . '.' . Entity::ATTR_MAGE_REF . ')');
        $query->where($where);
        /* fetch data */
        $result = $this->conn->fetchAll($query);
        return $result;
    }
}