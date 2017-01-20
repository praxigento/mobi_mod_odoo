<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

use Magento\CatalogInventory\Api\StockItemCriteriaInterface;

class Warehouse
{
    /** @var   \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Praxigento\Odoo\Repo\IRegistry */
    protected $repoRegistry;
    /** @var  \Magento\CatalogInventory\Api\StockItemRepositoryInterface */
    protected $repoStockItem;
    /** @var  Warehouse\DataHandler */
    protected $subDataHandler;
    /** @var  \Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Lot */
    protected $subLot;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Repo\IRegistry $repoRegistry,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $repoStockItem,
        \Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Lot $subLot,
        \Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Warehouse\DataHandler $subDataHandler
    ) {
        $this->manObj = $manObj;
        $this->repoRegistry = $repoRegistry;
        $this->repoStockItem = $repoStockItem;
        $this->subLot = $subLot;
        $this->subDataHandler = $subDataHandler;
    }

    /**
     * Get stock items by product ID (mage).
     * @param int $prodId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface[]
     */
    private function _getStockItems($prodId)
    {
        /** @var StockItemCriteriaInterface $crit */
        $crit = $this->manObj->create(StockItemCriteriaInterface::class);
        $crit->setProductsFilter($prodId);
        /* get all stock items and create map by stock id (warehouse ID)*/
        /** @var \Magento\CatalogInventory\Api\Data\StockItemCollectionInterface $list */
        $list = $this->repoStockItem->getList($crit);
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
            $stockIdMage = $this->repoRegistry->getWarehouseMageIdByOdooId($stockIdOdoo);
            /* create or update product data for warehouse (stock)*/
            if (isset($mapItemsByStock[$stockIdMage])) {
                /* there is item for the stock, update item data */
                $stocksFound[] = $stockIdMage;
                /* get stock item ID by stock ID */
                $stockItemIdMage = $mapItemsByStock[$stockIdMage];
                $stockItem = $stockItems[$stockItemIdMage];
                /* update warehouse price & PV */
                $this->subDataHandler->updateWarehouseData($stockItemIdMage, $priceWarehouse, $pvWarehouse);
            } else {
                /* there is no item for the stock, create new item */
                $stockItem = $this->subDataHandler
                    ->createWarehouseData($productIdMage, $stockIdMage, $priceWarehouse, $pvWarehouse);
            }
            /* create or update lot/quantity data */
            $lots = $warehouse->getLots();
            $this->subDataHandler->processLots($lots, $stockItem);
            /* process Custermer groups prices */
            $prices = $warehouse->getPrices();
            $this->subDataHandler->processPrices($prices, $stockItem);
        }
    }
}