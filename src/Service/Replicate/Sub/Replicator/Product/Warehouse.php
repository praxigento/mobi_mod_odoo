<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockItemCriteriaInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity\Lot as EntityLot;
use Praxigento\Odoo\Data\Entity\Warehouse as EntityWarehouse;
use Praxigento\Odoo\Repo\Agg\IWarehouse as RepoAggIWarehouse;
use Praxigento\Odoo\Repo\IModule;
use Praxigento\Odoo\Repo\IPvModule as IRepoPvModule;
use Praxigento\Pv\Repo\Entity\Stock\IItem as IRepoPvStockItem;
use Praxigento\Warehouse\Repo\Entity\IQuantity as IRepoWarehouseEntityQuantity;
use Praxigento\Warehouse\Repo\Entity\Stock\IItem as IRepoWarehouseEntityStockItem;

class Warehouse
{
    /** @var   StockItemRepositoryInterface */
    protected $_mageRepoStockItem;
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var  RepoAggIWarehouse */
    protected $_repoAggWarehouse;
    /** @var IModule */
    protected $_repoMod;
    /** @var  IRepoPvModule */
    protected $_repoPvMod;
    /** @var IRepoPvStockItem */
    protected $_repoPvStockItem;
    /** @var  IRepoWarehouseEntityLot */
    protected $_repoWarehouseEntityQuantity;
    /** @var  IRepoWarehouseEntityStockItem */
    protected $_repoWarehouseEntityStockItem;

    public function __construct(
        ObjectManagerInterface $manObj,
        StockItemRepositoryInterface $mageRepoStockItem,
        IModule $repoMod,
        RepoAggIWarehouse $repoAggWarehouse,
        IRepoPvModule $repoPvMod,
        IRepoWarehouseEntityStockItem $repoWarehouseEntityStockItem,
        IRepoWarehouseEntityQuantity $repoWarehouseEntityQuantity,
        IRepoPvStockItem $repoPvStockItem
    ) {
        $this->_manObj = $manObj;
        $this->_mageRepoStockItem = $mageRepoStockItem;
        $this->_repoMod = $repoMod;
        $this->_repoAggWarehouse = $repoAggWarehouse;
        $this->_repoPvMod = $repoPvMod;
        $this->_repoWarehouseEntityStockItem = $repoWarehouseEntityStockItem;
        $this->_repoWarehouseEntityQuantity = $repoWarehouseEntityQuantity;
        $this->_repoPvStockItem = $repoPvStockItem;
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
     * @param int $productIdMage
     * @param \Praxigento\Odoo\Api\Data\Bundle\Product\IWarehouse[] $warehouses
     */
    public function processWarehouses($productIdMage, $warehouses)
    {
        $refPvStockItem = $this->_repoPvStockItem->getRef();
        $refWrhsStockItem = $this->_repoWarehouseEntityStockItem->getRef();
        /** @var StockItemCriteriaInterface $crit */
        $crit = $this->_manObj->create(StockItemCriteriaInterface::class);
        $crit->setProductsFilter($productIdMage);
        /* get all stock items and create map by stock id (warehouse ID)*/
        $stockItems = $this->_mageRepoStockItem->getList($crit)->getItems();
        $stockIds = $this->_mapStockIds($stockItems);
        $stocksFound = [];    // array of the replicated warehouses with correspondence in $stockItems
        foreach ($warehouses as $warehouse) {
            $stockIdOdoo = $warehouse->getId();
            $pvWarehouse = $warehouse->getPv();
            $priceWarehouse = $warehouse->getPrice();
            /* get warehouse data by Odoo ID */
            $stockIdMage = $this->_repoMod->getMageIdByOdooId(EntityWarehouse::ENTITY_NAME, $stockIdOdoo);
            /* create or update product data for warehouse (stock)*/
            if (isset($stockIds[$stockIdMage])) {
                /* there is item for the stock, update item data */
                $stocksFound[] = $stockIdMage;
                /* get stock item ID by stock ID */
                $stockItemIdMage = $stockIds[$stockIdMage];
                $stockItem = $stockItems[$stockItemIdMage];
                /* update warehouse price */
                $bind = [$refWrhsStockItem::ATTR_PRICE => $priceWarehouse];
                $this->_repoWarehouseEntityStockItem->update($bind, $stockItemIdMage);
                /* update warehouse PV */
                $this->_repoPvMod->updateWarehousePv($stockItemIdMage, $pvWarehouse);
            } else {
                /* there is no item for the stock, create new item */
                /** @var StockItemInterface $stockItem */
                $stockItem = $this->_manObj->create(StockItemInterface::class);
                $stockItem->setProductId($productIdMage);
                $stockItem->setStockId($stockIdMage);
                $stockItem->setIsInStock(true);
                $stockItem->setManageStock(true);
                $stockItem = $this->_mageRepoStockItem->save($stockItem);
                $stockItemIdMage = $stockItem->getItemId();
                /* register warehouse price */
                $bind = [
                    $refWrhsStockItem::ATTR_STOCK_ITEM_REF => $stockItemIdMage,
                    $refWrhsStockItem::ATTR_PRICE => $priceWarehouse
                ];
                $this->_repoWarehouseEntityStockItem->create($bind);
                /* register warehouse PV */
                $bind = [
                    $refPvStockItem::ATTR_STOCK_ITEM_REF => $stockItemIdMage,
                    $refPvStockItem::ATTR_PV => $pvWarehouse
                ];
                $this->_repoPvStockItem->create($bind);
            }
            /* create or update lot/quantity data */
            $lots = $warehouse->getLots();
            $refQty = $this->_repoWarehouseEntityQuantity->getRef();
            $qtyTotal = 0;
            foreach ($lots as $lot) {
                $lotIdOdoo = $lot->getId();
                $qty = $lot->getQty();
                $qtyTotal += $qty;
                $lotIdMage = $this->_repoMod->getMageIdByOdooId(EntityLot::ENTITY_NAME, $lotIdOdoo);
                $pk = [$refQty::ATTR_STOCK_ITEM_REF => $stockItemIdMage, $refQty::ATTR_LOT_REF => $lotIdMage];
                $qtyItem = $this->_repoWarehouseEntityQuantity->getById($pk);
                if ($qtyItem) {
                    /* update lot qty data */
                    $bind = [$refQty::ATTR_TOTAL => $qty];
                    $this->_repoWarehouseEntityQuantity->update($bind, $pk);
                } else {
                    /* create lot qty data */
                    $pk[$refQty::ATTR_TOTAL] = $qty;
                    $this->_repoWarehouseEntityQuantity->create($pk);
                }
            }
            /* update stock item qty */
            $stockItem->setQty($qtyTotal);
            $this->_mageRepoStockItem->save($stockItem);
        }
    }
}