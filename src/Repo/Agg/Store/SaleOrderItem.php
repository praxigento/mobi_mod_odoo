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
    const PARAM_ORDER_ID = SaleOrderItem\SelectFactory::PARAM_ORDER_ID;
    const PARAM_STOCK_ID = SaleOrderItem\SelectFactory::PARAM_STOCK_ID;
    /**#@- */

    /** @var  SaleOrderItem\SelectFactory */
    protected $_factorySelect;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        SaleOrderItem\SelectFactory $factorySelect
    ) {
        $this->conn = $resource->getConnection();
        $this->_factorySelect = $factorySelect;
    }

    /** @inheritdoc */
    public function getByOrderAndStock($orderId, $stockId)
    {
        $result = [];
        $select = $this->_factorySelect->getQueryToSelect();
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
        $result = $this->_factorySelect->getQueryToSelect();
        return $result;
    }

    public function getQueryToSelectCount()
    {
        $result = $this->_factorySelect->getQueryToSelectCount();
        return $result;
    }
}