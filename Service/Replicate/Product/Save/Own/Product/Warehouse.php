<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product;

class Warehouse
{
    /** @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Item\StockItemCriteriaFactory */
    private $factStockItem;
    /** @var  \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler */
    private $ownHandler;
    /** @var \Praxigento\Odoo\Repo\Dao\Warehouse */
    private $repoOdooWrhs;
    /** @var  \Magento\CatalogInventory\Api\StockItemRepositoryInterface */
    private $repoStockItem;

    public function __construct(
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Item\StockItemCriteriaFactory $factStockItem,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $repoStockItem,
        \Praxigento\Odoo\Repo\Dao\Warehouse $repoOdooWrhs,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler $ownHandler
    ) {
        $this->factStockItem = $factStockItem;
        $this->repoStockItem = $repoStockItem;
        $this->repoOdooWrhs = $repoOdooWrhs;
        $this->ownHandler = $ownHandler;
    }

    /**
     * @param int $productIdMage
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\IWarehouse[] $warehouses
     */
    public function exec($productIdMage, $warehouses)
    {
        /* get all stock items and create map by stock id (warehouse ID)*/
        $stockItems = $this->getStockItems($productIdMage);
        $mapItemsByStock = $this->mapStockIds($stockItems);
        $stocksFound = [];    // array of the replicated warehouses with correspondence in $stockItems
        foreach ($warehouses as $warehouse) {
            $stockIdOdoo = $warehouse->getIdOdoo();
            $pvWarehouse = $warehouse->getPvWarehouse();
            $priceWarehouse = $warehouse->getPriceWarehouse();
            /* get warehouse data by Odoo ID */
            $stockIdMage = $this->repoOdooWrhs->getMageIdByOdooId($stockIdOdoo);
            /* create or update product data for warehouse (stock)*/
            if (isset($mapItemsByStock[$stockIdMage])) {
                /* there is item for the stock, update item data */
                $stocksFound[] = $stockIdMage;
                /* get stock item ID by stock ID */
                $stockItemIdMage = $mapItemsByStock[$stockIdMage];
                $stockItem = $stockItems[$stockItemIdMage];
                /* update warehouse price & PV */
                $this->ownHandler->updateWarehouseData($stockItemIdMage, $priceWarehouse, $pvWarehouse);
            } else {
                /* there is no item for the stock, create new item */
                $stockItem = $this->ownHandler
                    ->createWarehouseData($productIdMage, $stockIdMage, $priceWarehouse, $pvWarehouse);
            }
            /* create or update lot/quantity data */
            $lots = $warehouse->getLots();
            $this->ownHandler->processLots($lots, $stockItem);
            /* process Customer groups prices */
            $prices = $warehouse->getPrices();
            $this->ownHandler->processPrices($prices, $stockItem, $priceWarehouse);
        }
    }

    /**
     * Get stock items by product ID (mage).
     * @param int $prodId
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface[]
     */
    private function getStockItems($prodId)
    {
        /** @var \Magento\CatalogInventory\Api\StockItemCriteriaInterface $crit */
        $crit = $this->factStockItem->create();
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
    private function mapStockIds($stockItems)
    {
        $result = [];
        /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem */
        foreach ($stockItems as $stockItemId => $stockItem) {
            $stockId = $stockItem->getStockId();
            $result[$stockId] = $stockItemId;
        }
        return $result;
    }
}