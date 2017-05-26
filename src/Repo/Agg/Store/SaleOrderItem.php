<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Store;

use Praxigento\Odoo\Repo\Agg\Data\SaleOrderItem as Agg;

class SaleOrderItem
    extends \Praxigento\Core\Repo\Def\Crud
    implements \Praxigento\Odoo\Repo\Agg\Store\ISaleOrderItem
{
    /**#@+
     * Select query parameters names.
     */
    const PARAM_ORDER_ID = \Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get\Builder::BIND_ORDER_ID;
    const PARAM_STOCK_ID = \Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get\Builder::BIND_STOCK_ID;
    /**#@- */

    /**
     * @var  SaleOrderItem\SelectFactory
     *
     * @deprecated use $qbldGet
     */
    protected $factorySelect;
    /** @var \Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get\Builder */
    protected $qbldGet;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        SaleOrderItem\SelectFactory $factorySelect,
        \Praxigento\Odoo\Repo\Agg\Query\SaleOrderItem\Get\Builder $qbldGet
    ) {
        $this->conn = $resource->getConnection();
        $this->factorySelect = $factorySelect;
        $this->qbldGet = $qbldGet;
    }

    /** @inheritdoc */
    public function getByOrderAndStock($orderId, $stockId)
    {
        $result = [];
        $select = $this->qbldGet->build();
        $bind = [
            self::PARAM_ORDER_ID => (int)$orderId,
            self::PARAM_STOCK_ID => (int)$stockId
        ];
        $data = $this->conn->fetchAll($select, $bind);
        if ($data) {
            foreach ($data as $row) {
                $item = new Agg($row);
                $result[] = $item;
            }
        }
        return $result;
    }

    public function getQueryToSelect()
    {
        $result = $this->qbldGet->build();
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->factorySelect->getQueryToSelectCount();
        return $result;
    }
}