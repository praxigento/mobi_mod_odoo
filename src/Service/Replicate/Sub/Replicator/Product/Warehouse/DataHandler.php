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
    /** @var   StockItemRepositoryInterface */
    protected $_mageRepoStockItem;
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var  IRepoPvModule */
    protected $_repoPvMod;
    /** @var IRepoPvStockItem */
    protected $_repoPvStockItem;
    /** @var  IRepoWarehouseEntityStockItem */
    protected $_repoWarehouseEntityStockItem;
    /** @var  SubLot */
    protected $_subLot;

    public function __construct(
        ObjectManagerInterface $manObj,
        StockItemRepositoryInterface $mageRepoStockItem,
        IRepoPvModule $repoPvMod,
        IRepoWarehouseEntityStockItem $repoWarehouseEntityStockItem,
        IRepoPvStockItem $repoPvStockItem,
        SubLot $subLot
    ) {
        $this->_manObj = $manObj;
        $this->_mageRepoStockItem = $mageRepoStockItem;
        $this->_repoPvMod = $repoPvMod;
        $this->_repoWarehouseEntityStockItem = $repoWarehouseEntityStockItem;
        $this->_repoPvStockItem = $repoPvStockItem;
        $this->_subLot = $subLot;
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
        $result = $this->_manObj->create(StockItemInterface::class);
        $result->setProductId($prodId);
        $result->setStockId($stockId);
        $result->setIsInStock(true);
        $result->setManageStock(true);
        $result = $this->_mageRepoStockItem->save($result);
        $stockItemId = $result->getItemId();
        /* register warehouse price */
        $bind = [
            EntityWarehouseStockItem::ATTR_STOCK_ITEM_REF => $stockItemId,
            EntityWarehouseStockItem::ATTR_PRICE => $price
        ];
        $this->_repoWarehouseEntityStockItem->create($bind);
        /* register warehouse PV */
        $bind = [
            EntityPvStockItem::ATTR_STOCK_ITEM_REF => $stockItemId,
            EntityPvStockItem::ATTR_PV => $pv
        ];
        $this->_repoPvStockItem->create($bind);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\ILot[] $lots
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     */
    public function processLots($lots, $stockItem)
    {
        $qtyTotal = 0;
        $stockItemId = $stockItem->getItemId();
        foreach ($lots as $lot) {
            $qtyTotal += $this->_subLot->processLot($stockItemId, $lot);
        }
        /* update stock item qty */
        $stockItem->setQty($qtyTotal);
        $isInStock = ($qtyTotal > 0);
        $stockItem->setIsInStock($isInStock);
        $this->_mageRepoStockItem->save($stockItem);
        /* cleanup extra lots */
        $this->_subLot->cleanupLots($stockItemId, $lots);
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
        $exist = $this->_repoWarehouseEntityStockItem->getById($stockItemRef);
        if (!$exist) {
            /* create new entry */
            $bind[EntityWarehouseStockItem::ATTR_STOCK_ITEM_REF] = $stockItemRef;
            $this->_repoWarehouseEntityStockItem->create($bind);
        } else {
            $this->_repoWarehouseEntityStockItem->updateById($stockItemRef, $bind);
        }
        /* update or create warehouse PV */
        $registered = $this->_repoPvMod->getWarehousePv($stockItemRef);
        if (is_null($registered)) {
            /* create PV */
            $this->_repoPvMod->registerWarehousePv($stockItemRef, $pv);
        } else {
            /* update PV */
            $this->_repoPvMod->updateWarehousePv($stockItemRef, $pv);
        }
    }
}