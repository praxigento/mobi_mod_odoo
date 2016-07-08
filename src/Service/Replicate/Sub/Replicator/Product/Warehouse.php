<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

use Magento\CatalogInventory\Api\StockItemCriteriaInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Repo\IRegistry;

class Warehouse
{
    /** @var   StockItemRepositoryInterface */
    protected $_mageRepoStockItem;
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var IRegistry */
    protected $_repoRegistry;
    /** @var  Warehouse\DataHandler */
    protected $_subDataHandler;
    /** @var  Lot */
    protected $_subLot;

    public function __construct(
        ObjectManagerInterface $manObj,
        StockItemRepositoryInterface $mageRepoStockItem,
        IRegistry $repoRegistry,
        Lot $subLot,
        Warehouse\DataHandler $subDataHandler
    ) {
        $this->_manObj = $manObj;
        $this->_mageRepoStockItem = $mageRepoStockItem;
        $this->_repoRegistry = $repoRegistry;
        $this->_subLot = $subLot;
        $this->_subDataHandler = $subDataHandler;
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
        /** @var \Magento\CatalogInventory\Api\Data\StockItemCollectionInterface $list */
        $list = $this->_mageRepoStockItem->getList($crit);
        $result = $list->getItems();
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
     * @param int $productIdMage
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\IWarehouse[] $warehouses
     */
    public function processWarehouses($productIdMage, $warehouses)
    {
        /* get all stock items and create map by stock id (warehouse ID)*/
        $stockItems = $this->_getStockItems($productIdMage);
        $mapItemsByStock = $this->_mapStockIds($stockItems);
        $stocksFound = [];    // array of the replicated warehouses with correspondence in $stockItems
        foreach ($warehouses as $warehouse) {
            $stockIdOdoo = $warehouse->getIdOdoo();
            $pvWarehouse = $warehouse->getPvWarehouse();
            $priceWarehouse = $warehouse->getPriceWarehouse();
            /* get warehouse data by Odoo ID */
            $stockIdMage = $this->_repoRegistry->getWarehouseMageIdByOdooId($stockIdOdoo);
            /* create or update product data for warehouse (stock)*/
            if (isset($mapItemsByStock[$stockIdMage])) {
                /* there is item for the stock, update item data */
                $stocksFound[] = $stockIdMage;
                /* get stock item ID by stock ID */
                $stockItemIdMage = $mapItemsByStock[$stockIdMage];
                $stockItem = $stockItems[$stockItemIdMage];
                /* update warehouse price & PV */
                $this->_subDataHandler->updateWarehouseData($stockItemIdMage, $priceWarehouse, $pvWarehouse);
            } else {
                /* there is no item for the stock, create new item */
                $stockItem = $this->_subDataHandler
                    ->createWarehouseData($productIdMage, $stockIdMage, $priceWarehouse, $pvWarehouse);
            }
            /* create or update lot/quantity data */
            $lots = $warehouse->getLots();
            $this->_subDataHandler->processLots($lots, $stockItem);
        }
    }
}