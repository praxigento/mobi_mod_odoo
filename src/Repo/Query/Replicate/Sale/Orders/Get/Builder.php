<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Get;

use Praxigento\Core\Repo\Query\Expression;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Entity\SaleOrder as Entity;

/**
 * Build query to get sale orders to be pushed into Odoo.
 */
class Builder
    extends \Praxigento\Core\Repo\Query\Builder
{
    /** Tables aliases */
    const AS_ORDER = 'sales';
    const AS_REGISTRY = 'reg';

    /** Columns aliases */
    const A_ORDER_ID = 'orderId';

    /**
     * SELECT
     * `sales`.`entity_id` AS `orderId`
     * FROM `sales_order` AS `sales`
     * LEFT JOIN `prxgt_odoo_sale` AS `reg`
     * ON reg.mage_ref = sales.entity_id
     * WHERE ((ISNULL(reg.mage_ref)))
     *
     * @param \Magento\Framework\DB\Select|null $source
     */
    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* is this a root builder or a queued builder? */
        $result = is_null($source) ? $this->conn->select() : clone $source;

        /* define tables aliases */
        $asSaleOrder = self::AS_ORDER;
        $asOdooReg = self::AS_REGISTRY;

        /* SELECT FROM sales_order */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER);
        $as = $asSaleOrder;
        $cols = [self::A_ORDER_ID => Cfg::E_SALE_ORDER_A_ENTITY_ID];
        $result->from([$as => $tbl], $cols);

        /* LEFT OUTER JOIN prxgt_odoo_sale */
        $tbl = $this->resource->getTableName(Entity::ENTITY_NAME);
        $as = $asOdooReg;
        $cond = $as . '.' . Entity::ATTR_MAGE_REF . '=' . $asSaleOrder . '.' . Cfg::E_SALE_ORDER_A_ENTITY_ID;
        $cols = [];
        $result->joinLeft([$as => $tbl], $cond, $cols);
        /* WHERE */
        $where = new Expression('ISNULL(' . $asOdooReg . '.' . Entity::ATTR_MAGE_REF . ')');
        $result->where($where);

        return $result;
    }
}