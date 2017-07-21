<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Warehouse;

class DataHandler
{
    /** @var  array */
    protected $cachedCustomerGroupsIds;
    /** @var \Praxigento\Odoo\Tool\IBusinessCodesManager */
    protected $hlpBusCodes;
    /** @var   \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Magento\Customer\Api\GroupRepositoryInterface */
    protected $repoCustGroup;
    /** @var \Praxigento\Warehouse\Repo\Entity\Group\IPrice */
    protected $repoGroupPrice;
    /** @var  \Praxigento\Odoo\Repo\IPv */
    protected $repoPvMod;
    /** @var \Praxigento\Pv\Repo\Entity\Stock\Item */
    protected $repoPvStockItem;
    /** @var   \Magento\CatalogInventory\Api\StockItemRepositoryInterface */
    protected $repoStockItem;
    /** @var  \Praxigento\Warehouse\Repo\Entity\Stock\IItem */
    protected $repoWrhsStockItem;
    /** @var  \Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Lot */
    protected $subLot;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $repoStockItem,
        \Magento\Customer\Api\GroupRepositoryInterface $repoCustGroup,
        \Praxigento\Odoo\Repo\IPv $repoPvMod,
        \Praxigento\Warehouse\Repo\Entity\Stock\IItem $repoWrhsStockItem,
        \Praxigento\Pv\Repo\Entity\Stock\Item $repoPvStockItem,
        \Praxigento\Warehouse\Repo\Entity\Group\IPrice $repoGroupPrice,
        \Praxigento\Odoo\Tool\IBusinessCodesManager $hlpBusCodes,
        \Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product\Lot $subLot
    ) {
        $this->manObj = $manObj;
        $this->repoStockItem = $repoStockItem;
        $this->repoCustGroup = $repoCustGroup;
        $this->repoPvMod = $repoPvMod;
        $this->repoWrhsStockItem = $repoWrhsStockItem;
        $this->repoPvStockItem = $repoPvStockItem;
        $this->repoGroupPrice = $repoGroupPrice;
        $this->hlpBusCodes = $hlpBusCodes;
        $this->subLot = $subLot;
    }

    /**
     * Create new warehouse data for the product in Magento.
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
        $result = $this->manObj->create(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $result->setProductId($prodId);
        $result->setStockId($stockId);
        $result->setIsInStock(true);
        $result->setManageStock(true);
        $result = $this->repoStockItem->save($result);
        $stockItemId = $result->getItemId();
        /* register warehouse price */
        $bind = [
            \Praxigento\Warehouse\Data\Entity\Stock\Item::ATTR_STOCK_ITEM_REF => $stockItemId,
            \Praxigento\Warehouse\Data\Entity\Stock\Item::ATTR_PRICE => $price
        ];
        $this->repoWrhsStockItem->create($bind);
        /* register warehouse PV */
        $bind = [
            \Praxigento\Pv\Data\Entity\Stock\Item::ATTR_STOCK_ITEM_REF => $stockItemId,
            \Praxigento\Pv\Data\Entity\Stock\Item::ATTR_PV => $pv
        ];
        $this->repoPvStockItem->create($bind);
        return $result;
    }

    protected function getCustomerGroupsIds()
    {
        if (is_null($this->cachedCustomerGroupsIds)) {
            $this->cachedCustomerGroupsIds = [];
            $crit = new \Magento\Framework\Api\SearchCriteria();
            $list = $this->repoCustGroup->getList($crit);
            /** @var \Magento\Customer\Model\Data\Group $item */
            foreach ($list->getItems() as $item) {
                $id = $item->getId();
                $this->cachedCustomerGroupsIds[] = $id;
            }
        }
        return $this->cachedCustomerGroupsIds;
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
     * Process custoemr group prices for warehouses.
     *
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\GroupPrice\Item[] $prices
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param float $priceWarehouse (MOBI-734)
     */
    public function processPrices($prices, $stockItem, $priceWarehouse)
    {
        $stockItemId = $stockItem->getItemId();
        /* MOBI-734: get customer groups  */
        $groupsIdsAll = $this->getCustomerGroupsIds();
        $groupsIdsProcessed = [];
        /* cleanup prices */
        $where = \Praxigento\Warehouse\Data\Entity\Group\Price::ATTR_STOCK_ITEM_REF . '=' . (int)$stockItemId;
        $this->repoGroupPrice->delete($where);
        /* save new prices */
        $data = new \Praxigento\Warehouse\Data\Entity\Group\Price();
        $data->setStockItemRef($stockItemId);
        foreach ($prices as $item) {
            $price = $item->getPrice();
            $groupCode = $item->getGroupCode();
            $groupId = $this->hlpBusCodes->getMageIdForCustomerGroupByCode($groupCode);
            $groupsIdsProcessed[] = $groupId;
            /* don't save prices for unknown groups */
            if (!is_null($groupId)) {
                $data->setCustomerGroupRef($groupId);
                $data->setPrice($price);
                $this->repoGroupPrice->create($data);
            }
        }
        /* MOBI-734: set warehouse price ad default for missed groups */
        foreach ($groupsIdsAll as $groupId) {
            if (!in_array($groupId, $groupsIdsProcessed)) {
                $data->setCustomerGroupRef($groupId);
                $data->setPrice($priceWarehouse);
                $this->repoGroupPrice->create($data);
            }
        }
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
        $bind = [\Praxigento\Warehouse\Data\Entity\Stock\Item::ATTR_PRICE => $price];
        $exist = $this->repoWrhsStockItem->getById($stockItemRef);
        if (!$exist) {
            /* create new entry */
            $bind[\Praxigento\Warehouse\Data\Entity\Stock\Item::ATTR_STOCK_ITEM_REF] = $stockItemRef;
            $this->repoWrhsStockItem->create($bind);
        } else {
            $this->repoWrhsStockItem->updateById($stockItemRef, $bind);
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