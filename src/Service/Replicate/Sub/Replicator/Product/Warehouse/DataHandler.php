<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Warehouse;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Repo\IPv as IRepoPvModule;
use Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Lot as SubLot;
use Praxigento\Pv\Data\Entity\Stock\Item as EntityPvStockItem;
use Praxigento\Pv\Repo\Entity\Stock\IItem as IRepoPvStockItem;
use Praxigento\Warehouse\Data\Entity\Stock\Item as EntityWarehouseStockItem;
use Praxigento\Warehouse\Repo\Entity\Stock\IItem as IRepoWarehouseEntityStockItem;

class DataHandler
{
    /** @var   ObjectManagerInterface */
    protected $manObj;
    /** @var  IRepoPvModule */
    protected $repoPvMod;
    /** @var IRepoPvStockItem */
    protected $repoPvStockItem;
    /** @var   StockItemRepositoryInterface */
    protected $repoStockItem;
    /** @var  IRepoWarehouseEntityStockItem */
    protected $repoWarehouseEntityStockItem;
    /** @var  SubLot */
    protected $subLot;

    public function __construct(
        ObjectManagerInterface $manObj,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface\Proxy $mageRepoStockItem,
        IRepoPvModule $repoPvMod,
        IRepoWarehouseEntityStockItem $repoWarehouseEntityStockItem,
        IRepoPvStockItem $repoPvStockItem,
        SubLot $subLot
    ) {
        $this->manObj = $manObj;
        $this->repoStockItem = $mageRepoStockItem;
        $this->repoPvMod = $repoPvMod;
        $this->repoWarehouseEntityStockItem = $repoWarehouseEntityStockItem;
        $this->repoPvStockItem = $repoPvStockItem;
        $this->subLot = $subLot;
    }

    /**
     * Create new warehouse data for the product in Magento.
     *
     * @param int $prodId
     * @param int $stockId
     * @param double $price
     * @param double $pv
     * @return StockItemInterface
     */
    public function createWarehouseData($prodId, $stockId, $price, $pv)
    {
        /** @var StockItemInterface $result */
        $result = $this->manObj->create(StockItemInterface::class);
        $result->setProductId($prodId);
        $result->setStockId($stockId);
        $result->setIsInStock(true);
        $result->setManageStock(true);
        $result = $this->repoStockItem->save($result);
        $stockItemId = $result->getItemId();
        /* register warehouse price */
        $bind = [
            EntityWarehouseStockItem::ATTR_STOCK_ITEM_REF => $stockItemId,
            EntityWarehouseStockItem::ATTR_PRICE => $price
        ];
        $this->repoWarehouseEntityStockItem->create($bind);
        /* register warehouse PV */
        $bind = [
            EntityPvStockItem::ATTR_STOCK_ITEM_REF => $stockItemId,
            EntityPvStockItem::ATTR_PV => $pv
        ];
        $this->repoPvStockItem->create($bind);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\Lot[] $lots
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     */
    public function processLots($lots, $stockItem)
    {
        $qtyTotal = 0;
        $stockItemId = $stockItem->getItemId();
        foreach ($lots as $lot) {
            $qtyTotal += $this->subLot->processLot($stockItemId, $lot);
        }
        /* update stock item qty */
        $stockItem->setQty($qtyTotal);
        $isInStock = ($qtyTotal > 0);
        $stockItem->setIsInStock($isInStock);
        $this->repoStockItem->save($stockItem);
        /* cleanup extra lots */
        $this->subLot->cleanupLots($stockItemId, $lots);
    }

    /**
     * Create existing warehouse data for the stock item in Magento.
     *
     * @param int $stockItemRef Mage ID for related stock item
     * @param double $price warehouse price for stock item
     * @param double $pv warehouse PV for stock item
     */
    public function updateWarehouseData($stockItemRef, $price, $pv)
    {
        /* update or create warehouse entry */
        $bind = [EntityWarehouseStockItem::ATTR_PRICE => $price];
        $exist = $this->repoWarehouseEntityStockItem->getById($stockItemRef);
        if (!$exist) {
            /* create new entry */
            $bind[EntityWarehouseStockItem::ATTR_STOCK_ITEM_REF] = $stockItemRef;
            $this->repoWarehouseEntityStockItem->create($bind);
        } else {
            $this->repoWarehouseEntityStockItem->updateById($stockItemRef, $bind);
        }
        /* update or create warehouse PV */
        $registered = $this->repoPvMod->getWarehousePv($stockItemRef);
        if (is_null($registered)) {
            /* create PV */
            $this->repoPvMod->registerWarehousePv($stockItemRef, $pv);
        } else {
            /* update PV */
            $this->repoPvMod->updateWarehousePv($stockItemRef, $pv);
        }
    }
}