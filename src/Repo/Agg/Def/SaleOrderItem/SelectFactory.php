<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Agg\Def\SaleOrderItem;

use Magento\Sales\Api\Data\OrderItemInterface as MageEntityOrderItem;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Agg\SaleOrderItem as Agg;
use Praxigento\Odoo\Data\Entity\Lot as EntityOdooLot;
use Praxigento\Odoo\Data\Entity\Product as EntityOdooProduct;
use Praxigento\Pv\Data\Entity\Sale\Item as EntityPvSaleItem;
use Praxigento\Pv\Data\Entity\Stock\Item as EntityPvStockItem;
use Praxigento\Warehouse\Data\Entity\Quantity\Sale as EntityWrhsQtySale;

/**
 * Compose SELECT query to get Sale Order Item aggregate.
 */
class SelectFactory implements \Praxigento\Core\Repo\Query\IHasSelect
{
    /**#@+
     * Query parameters names.
     */
    const PARAM_ORDER_ID = 'order_id';
    const PARAM_STOCK_ID = 'stock_id';
    /**#@- */

    /** @var  \Magento\Framework\DB\Adapter\AdapterInterface */
    protected $_conn;
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $_resource;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_logger = $logger;
        $this->_resource = $resource;
        $this->_conn = $resource->getConnection();
    }

    public function getSelectCountQuery()
    {
        throw new \Exception("this method is not implemented yet.");
    }

    /**
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectQuery()
    {
        $result = $this->_conn->select();
        /* aliases and tables */
        $asSaleItem = 'saleItem';
        $asStockItem = 'stockItem';
        $asPvSaleItem = 'pvSaleItem';
        $asPvStockItem = 'pvStockItem';
        $asQtySale = 'wrhsQtySale';
        $asOdooProd = 'odooProd';
        $asOdooLot = 'odooLot';
        $tblSaleItem = [$asSaleItem => $this->_resource->getTableName(Cfg::ENTITY_MAGE_SALES_ORDER_ITEM)];
        $tblStockItem = [$asStockItem => $this->_resource->getTableName(Cfg::ENTITY_MAGE_CATALOGINVENTORY_STOCK_ITEM)];
        $tblPvSaleItem = [$asPvSaleItem => EntityPvSaleItem::ENTITY_NAME];
        $tblPvStockItem = [$asPvStockItem => EntityPvStockItem::ENTITY_NAME];
        $tblQtySale = [$asQtySale => EntityWrhsQtySale::ENTITY_NAME];
        $tblOdooProd = [$asOdooProd => EntityOdooProduct::ENTITY_NAME];
        $tblOdooLot = [$asOdooLot => EntityOdooLot::ENTITY_NAME];
        /* FROM sales_order_item */
        $cols = [
            Agg::AS_ITEM_QTY => MageEntityOrderItem::QTY_ORDERED,
            Agg::AS_PRICE_DISCOUNT => MageEntityOrderItem::BASE_DISCOUNT_AMOUNT,
            Agg::AS_PRICE_TAX_PERCENT => MageEntityOrderItem::TAX_PERCENT,
            Agg::AS_PRICE_TOTAL => MageEntityOrderItem::BASE_ROW_TOTAL,
            Agg::AS_PRICE_TOTAL_WITH_TAX => MageEntityOrderItem::BASE_ROW_TOTAL_INCL_TAX,
            Agg::AS_PRICE_UNIT_ORIG => MageEntityOrderItem::BASE_ORIGINAL_PRICE,
            Agg::AS_PRICE_UNIT => MageEntityOrderItem::BASE_PRICE_INCL_TAX,
        ];
        $result->from($tblSaleItem, $cols);
        /* LEFT JOIN cataloginventory_stock_item */
        $cols = [];
        $on = $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID . '=' . $asSaleItem . '.' . MageEntityOrderItem::PRODUCT_ID;
        $on .= ' AND ' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID . '=:' . self::PARAM_STOCK_ID;
        $result->joinLeft($tblStockItem, $on, $cols);
        /* LEFT JOIN prxgt_odoo_prod */
        $cols = [
            Agg::AS_ODOO_ID_PROD => EntityOdooProduct::ATTR_ODOO_REF
        ];
        $on = $asOdooProd . '.' . EntityOdooProduct::ATTR_MAGE_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_PROD_ID;
        $result->joinLeft($tblOdooProd, $on, $cols);
        /* LEFT JOIN prxgt_pv_sale_item */
        $cols = [
            Agg::AS_PV_SUBTOTAL => EntityPvSaleItem::ATTR_SUBTOTAL,
            Agg::AS_PV_DISCOUNT => EntityPvSaleItem::ATTR_DISCOUNT,
            Agg::AS_PV_TOTAL => EntityPvSaleItem::ATTR_TOTAL
        ];
        $on = $asPvSaleItem . '.' . EntityPvSaleItem::ATTR_SALE_ITEM_ID . '=' . $asSaleItem . '.' . MageEntityOrderItem::ITEM_ID;
        $result->joinLeft($tblPvSaleItem, $on, $cols);
        /* LEFT JOIN prxgt_pv_stock_item */
        $cols = [
            Agg::AS_PV_UNIT => EntityPvStockItem::ATTR_PV
        ];
        $on = $asPvStockItem . '.' . EntityPvStockItem::ATTR_STOCK_ITEM_REF . '=' . $asStockItem . '.' . Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        $result->joinLeft($tblPvStockItem, $on, $cols);
        /* LEFT JOIN prxgt_wrhs_qty_sale */
        $cols = [
            Agg::AS_LOT_QTY => EntityWrhsQtySale::ATTR_TOTAL
        ];
        $on = $asQtySale . '.' . EntityWrhsQtySale::ATTR_SALE_ITEM_REF . '=' . $asSaleItem . '.' . MageEntityOrderItem::ITEM_ID;
        $result->joinLeft($tblQtySale, $on, $cols);
        /* LEFT JOIN prxgt_odoo_lot */
        $cols = [
            Agg::AS_ODOO_ID_LOT => EntityOdooLot::ATTR_ODOO_REF
        ];
        $on = $asOdooLot . '.' . EntityOdooLot::ATTR_MAGE_REF . '=' . $asQtySale . '.' . EntityWrhsQtySale::ATTR_LOT_REF;
        $result->joinLeft($tblOdooLot, $on, $cols);
        /* WHERE ... */
        $where = $asSaleItem . '.' . MageEntityOrderItem::ORDER_ID . '=:' . self::PARAM_ORDER_ID;
        $result->where($where);
        /* log result SQL */
        $this->_logger->info((string)$result);
        return $result;
    }
}