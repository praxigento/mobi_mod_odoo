<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse;

/**
 * Process product data related to warehouse (prices & PVs).
 */
class Handler
{

    /** @var \Magento\CatalogInventory\Model\Stock\ItemFactory */
    private $factStockItem;
    /** @var  \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler\Lot */
    private $ownLot;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler\Price */
    private $ownPrice;
    /** @var \Praxigento\Pv\Repo\Dao\Stock\Item */
    private $daoPvStockItem;
    /** @var   \Magento\CatalogInventory\Api\StockItemRepositoryInterface */
    private $daoStockItem;
    /** @var  \Praxigento\Warehouse\Repo\Dao\Stock\Item */
    private $daoWrhsStockItem;

    public function __construct(
        \Magento\CatalogInventory\Model\Stock\ItemFactory $factStockItem,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $daoStockItem,
        \Praxigento\Warehouse\Repo\Dao\Stock\Item $daoWrhsStockItem,
        \Praxigento\Pv\Repo\Dao\Stock\Item $daoPvStockItem,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler\Lot $ownLot,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler\Price $ownPrice
    ) {
        $this->factStockItem = $factStockItem;
        $this->daoStockItem = $daoStockItem;
        $this->daoWrhsStockItem = $daoWrhsStockItem;
        $this->daoPvStockItem = $daoPvStockItem;
        $this->ownLot = $ownLot;
        $this->ownPrice = $ownPrice;
    }

    /**
     * Create new warehouse data (price & PV) for the product in Magento.
     *
     * @param int $prodId
     * @param int $stockId
     * @param double $price
     * @param double $pv
     * @return \Magento\CatalogInventory\Api\Data\StockItemInterface
     */
    public function createWarehouseData($prodId, $stockId, $price, $pv)
    {
        /** @var \Magento\CatalogInventory\Api\Data\StockItemInterface $result */
        $result = $this->factStockItem->create();
        $result->setProductId($prodId);
        $result->setStockId($stockId);
        $result->setIsInStock(true);
        $result->setManageStock(true);
        $result = $this->daoStockItem->save($result);
        $stockItemId = $result->getItemId();
        /* register warehouse price */
        $entityPrice = new \Praxigento\Warehouse\Repo\Data\Stock\Item();
        $entityPrice->setStockItemRef($stockItemId);
        $entityPrice->setPrice($price);
        $this->daoWrhsStockItem->create($entityPrice);
        /* register warehouse PV */
        $entityPv = new \Praxigento\Pv\Repo\Data\Stock\Item();
        $entityPv->setItemRef($stockItemId);
        $entityPv->setPv($pv);
        $this->daoPvStockItem->create($entityPv);
        return $result;
    }

    private function getWarehousePv($stockItemMageId)
    {
        $result = null;
        $data = $this->daoPvStockItem->getById($stockItemMageId);
        if ($data) {
            $result = $data->getPv();
        }
        return $result;
    }

    /**
     * Replicate lots quantities from Odoo to Magento.
     *
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\Lot[] $lots
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     */
    public function processLots($lots, $stockItem)
    {
        $qtyTotal = 0;
        $stockItemId = $stockItem->getItemId();
        foreach ($lots as $lot) {
            $qtyTotal += $this->ownLot->save($stockItemId, $lot);
        }
        /* update stock item qty */
        $stockItem->setQty($qtyTotal);
        $isInStock = ($qtyTotal > 0);
        $stockItem->setIsInStock($isInStock);
        $this->daoStockItem->save($stockItem);
        /* cleanup extra lots */
        $this->ownLot->cleanup($stockItemId, $lots);
    }

    /**
     * Process customer group prices for warehouses.
     *
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\GroupPrice\Item[] $prices
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param float $priceWarehouse (MOBI-734)
     */
    public function processPrices($prices, $stockItem, $priceWarehouse)
    {
        $this->ownPrice->exec($prices, $stockItem, $priceWarehouse);
    }

    private function registerWarehousePv($stockItemMageId, $pv)
    {
        $entity = new \Praxigento\Pv\Repo\Data\Stock\Item();
        $entity->setItemRef($stockItemMageId);
        $entity->setPv($pv);
        $this->daoPvStockItem->create($entity);
    }

    /**
     * Create existing warehouse data for the stock item in Magento.
     *
     * @param int $stockItemRef Mage ID for related stock item
     * @param float $price warehouse price for stock item
     * @param float $pv warehouse PV for stock item
     */
    public function updateWarehouseData($stockItemRef, $price, $pv)
    {
        /* update or create warehouse entry */
        $bind = [\Praxigento\Warehouse\Repo\Data\Stock\Item::A_PRICE => $price];
        $exist = $this->daoWrhsStockItem->getById($stockItemRef);
        if (!$exist) {
            /* create new entry */
            $bind[\Praxigento\Warehouse\Repo\Data\Stock\Item::A_STOCK_ITEM_REF] = $stockItemRef;
            $this->daoWrhsStockItem->create($bind);
        } else {
            $this->daoWrhsStockItem->updateById($stockItemRef, $bind);
        }
        /* update or create warehouse PV */
        $registered = $this->getWarehousePv($stockItemRef);
        if (is_null($registered)) {
            /* create PV */
            $this->registerWarehousePv($stockItemRef, $pv);
        } else {
            /* update PV */
            $this->updateWarehousePv($stockItemRef, $pv);
        }
    }

    private function updateWarehousePv($stockItemMageId, $pv)
    {
        $bind = [
            \Praxigento\Pv\Repo\Data\Stock\Item::A_PV => $pv
        ];
        $where = \Praxigento\Pv\Repo\Data\Stock\Item::A_ITEM_REF . '=' . (int)$stockItemMageId;
        $this->daoPvStockItem->update($bind, $where);
    }
}