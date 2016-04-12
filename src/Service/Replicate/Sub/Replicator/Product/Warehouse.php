<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Odoo\Repo\IModule;
use Praxigento\Odoo\Repo\IPvModule as IRepoPvModule;
use Praxigento\Pv\Repo\Entity\Stock\IItem as IRepoPvStockItem;
use Praxigento\Warehouse\Repo\Entity\Stock\IItem as IRepoWarehouseEntityStockItem;

class Warehouse
{
    /** @var   StockItemRepositoryInterface */
    protected $_mageRepoStockItem;
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var IModule */
    protected $_repoMod;
    /** @var  IRepoPvModule */
    protected $_repoPvMod;
    /** @var IRepoPvStockItem */
    protected $_repoPvStockItem;
    /** @var  IRepoWarehouseEntityStockItem */
    protected $_repoWarehouseEntityStockItem;
    /** @var  Lot */
    protected $_subLot;

    public function __construct(
        ObjectManagerInterface $manObj,
        StockItemRepositoryInterface $mageRepoStockItem,
        IModule $repoMod,
        IRepoPvModule $repoPvMod,
        IRepoWarehouseEntityStockItem $repoWarehouseEntityStockItem,
        IRepoPvStockItem $repoPvStockItem,
        Lot $subLot
    ) {
        $this->_manObj = $manObj;
        $this->_mageRepoStockItem = $mageRepoStockItem;
        $this->_repoMod = $repoMod;
        $this->_repoPvMod = $repoPvMod;
        $this->_repoWarehouseEntityStockItem = $repoWarehouseEntityStockItem;
        $this->_repoPvStockItem = $repoPvStockItem;
        $this->_subLot = $subLot;
    }

    /**
     * Create new stock item and register warehouse related price & PV.
     *
     * @param int $prodId Magento ID for related product
     * @param int $stockId Magento ID for related stock/warehouse
     * @param double $price warehouse price for the product
     * @param double $pv warehouse PV for the product
     * @return StockItemInterface new stock item
     */
    private function _createWarehouseData($prodId, $stockId, $price, $pv)
    {
        $refPv = $this->_repoPvStockItem->getRef();
        $refWrhs = $this->_repoWarehouseEntityStockItem->getRef();
        /** @var StockItemInterface $result */
        $result = $this->_manObj->create(StockItemInterface::class);
        $result->setProductId($prodId);
        $result->setStockId($stockId);
        $result->setIsInStock(true);
        $result->setManageStock(true);
        $result = $this->_mageRepoStockItem->save($result);
        $stockItemId = $result->getItemId();
        /* register warehouse price */
        $bind = [
            $refWrhs::ATTR_STOCK_ITEM_REF => $stockItemId,
            $refWrhs::ATTR_PRICE => $price
        ];
        $this->_repoWarehouseEntityStockItem->create($bind);
        /* register warehouse PV */
        $bind = [
            $refPv::ATTR_STOCK_ITEM_REF => $stockItemId,
            $refPv::ATTR_PV => $pv
        ];
        $this->_repoPvStockItem->create($bind);
        return $result;
    }

    /**
     * Get stock items by product ID (mage).
     * @param int $prodId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface[]
     */
    private function _getStockItems($prodId)
    {
        /** @var StockItemCriteriaInterface $crit */
        $crit = $this->_manObj->create(StockItemCriteriaInterface::class);
        $crit->setProductsFilter($prodId);
        /* get all stock items and create map by stock id (warehouse ID)*/
        $result = $this->_mageRepoStockItem->getList($crit)->getItems();
        return $result;
    }

    /**
     * Compose map [$stockId => $stockItemId] for stock items found.
     *
     * @param \Magento\CatalogInventory\Api\Data\StockItemCollectionInterface $stockItems
     * @return int[]
     */
    private function _mapStockIds($stockItems)
    {
        $result = [];
        /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem */
        foreach ($stockItems as $stockItemId => $stockItem) {
            $stockId = $stockItem->getStockId();
            $result[$stockId] = $stockItemId;
        }
        return $result;
    }

    /**
     * @param int $stockItemId Mage ID for related stock item
     * @param $price warehouse price for stock item
     * @param $pv warehouse PV for stock item
     */
    private function _updateWarehouseData($stockItemId, $price, $pv)
    {
        $ref = $this->_repoWarehouseEntityStockItem->getRef();
        $bind = [$ref::ATTR_PRICE => $price];
        $this->_repoWarehouseEntityStockItem->update($bind, $stockItemId);
        /* update warehouse PV */
        $this->_repoPvMod->updateWarehousePv($stockItemId, $pv);
    }

    /**
     * @param int $productIdMage
     * @param \Praxigento\Odoo\Api\Data\Bundle\Product\IWarehouse[] $warehouses
     */
    public function processWarehouses($productIdMage, $warehouses)
    {
        /* get all stock items and create map by stock id (warehouse ID)*/
        $stockItems = $this->_getStockItems($productIdMage);
        $mapItemsByStock = $this->_mapStockIds($stockItems);
        $stocksFound = [];    // array of the replicated warehouses with correspondence in $stockItems
        foreach ($warehouses as $warehouse) {
            $stockIdOdoo = $warehouse->getId();
            $pvWarehouse = $warehouse->getPv();
            $priceWarehouse = $warehouse->getPrice();
            /* get warehouse data by Odoo ID */
            $stockIdMage = $this->_repoMod->getMageIdByOdooId(EntityWarehouse::ENTITY_NAME, $stockIdOdoo);
            /* create or update product data for warehouse (stock)*/
            if (isset($mapItemsByStock[$stockIdMage])) {
                /* there is item for the stock, update item data */
                $stocksFound[] = $stockIdMage;
                /* get stock item ID by stock ID */
                $stockItemIdMage = $mapItemsByStock[$stockIdMage];
                $stockItem = $stockItems[$stockItemIdMage];
                /* update warehouse price & PV */
                $this->_updateWarehouseData($stockItemIdMage, $priceWarehouse, $pvWarehouse);
            } else {
                /* there is no item for the stock, create new item */
                $stockItem = $this->_createWarehouseData($productIdMage, $stockIdMage, $priceWarehouse, $pvWarehouse);
                $stockItemIdMage = $stockItem->getItemId();
            }
            /* create or update lot/quantity data */
            $lots = $warehouse->getLots();
            $qtyTotal = 0;
            foreach ($lots as $lot) {
                $qtyTotal += $this->_subLot->processLot($stockItemIdMage, $lot);
            }
            /* update stock item qty */
            $stockItem->setQty($qtyTotal);
            $this->_mageRepoStockItem->save($stockItem);
            /* cleanup extra lots */
            $this->_subLot->cleanupLots($stockItemIdMage, $lots);
        }
    }
}