<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler;

use Praxigento\Warehouse\Repo\Entity\Data\Group\Price as EWrhsGroupPrice;

class Price
{
    /** @var  array */
    private static $cacheCustomerGroupsIds;

    /** @var \Magento\Framework\Api\Search\SearchCriteriaFactory */
    private $factSearchCrit;
    /** @var \Praxigento\Odoo\Tool\IBusinessCodesManager */
    private $hlpBusCodes;
    /** @var \Magento\Customer\Api\GroupRepositoryInterface */
    private $repoCustGroup;
    /** @var \Praxigento\Warehouse\Repo\Entity\Group\Price */
    private $repoGroupPrice;

    public function __construct(
        \Magento\Framework\Api\Search\SearchCriteriaFactory $factSearchCrit,
        \Magento\Customer\Api\GroupRepositoryInterface $repoCustGroup,
        \Praxigento\Warehouse\Repo\Entity\Group\Price $repoGroupPrice,
        \Praxigento\Odoo\Tool\IBusinessCodesManager $hlpBusCodes
    ) {
        $this->factSearchCrit = $factSearchCrit;
        $this->repoCustGroup = $repoCustGroup;
        $this->repoGroupPrice = $repoGroupPrice;
        $this->hlpBusCodes = $hlpBusCodes;
    }

    /**
     * Remove all group prices for the stock item.
     *
     * @param int $stockItemId
     */
    private function cleanup($stockItemId)
    {
        $where = EWrhsGroupPrice::ATTR_STOCK_ITEM_REF . '=' . (int)$stockItemId;
        $this->repoGroupPrice->delete($where);
    }

    /**
     * Process customer group prices for warehouses.
     *
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\GroupPrice\Item[] $prices
     * @param \Magento\CatalogInventory\Api\Data\StockItemInterface $stockItem
     * @param float $priceWarehouse (MOBI-734)
     */
    public function exec($prices, $stockItem, $priceWarehouse)
    {
        $stockItemId = $stockItem->getItemId();
        /* remove all warehouse group prices for the stock item */
        $this->cleanup($stockItemId);
        /* save new group prices received from Odoo */
        $groupsIdsProcessed = $this->saveReceived($prices, $stockItemId);
        /* set warehouse price for missed groups */
        $this->saveMissed($stockItemId, $priceWarehouse, $groupsIdsProcessed);
    }

    private function getCustomerGroupsIds()
    {
        if (is_null(self::$cacheCustomerGroupsIds)) {
            self::$cacheCustomerGroupsIds = [];
            /** @var \Magento\Framework\Api\SearchCriteria $crit */
            $crit = $this->factSearchCrit->create();
            $list = $this->repoCustGroup->getList($crit);
            /** @var \Magento\Customer\Model\Data\Group $item */
            foreach ($list->getItems() as $item) {
                $id = $item->getId();
                self::$cacheCustomerGroupsIds[] = $id;
            }
        }
        return self::$cacheCustomerGroupsIds;
    }

    /**
     * MOBI-734: set warehouse price as default for missed groups.
     *
     * @param int $stockItemId
     * @param float $priceWarehouse
     * @param int[] $processedGroupIds
     */
    private function saveMissed($stockItemId, $priceWarehouse, $processedGroupIds)
    {
        /*  */
        $groupsIdsAll = $this->getCustomerGroupsIds();
        $data = new EWrhsGroupPrice();
        $data->setStockItemRef($stockItemId);
        foreach ($groupsIdsAll as $groupId) {
            if (!in_array($groupId, $processedGroupIds)) {
                $data->setCustomerGroupRef($groupId);
                $data->setPrice($priceWarehouse);
                $this->repoGroupPrice->create($data);
            }
        }
    }

    /**
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\GroupPrice\Item[] $prices
     * @param int $stockItemId
     * @return int[] IDs for processed customer groups.
     */
    private function saveReceived($prices, $stockItemId)
    {
        $result = [];
        /* save new prices */
        $data = new EWrhsGroupPrice();
        $data->setStockItemRef($stockItemId);
        /** @var \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\GroupPrice\Item $item */
        foreach ($prices as $item) {
            $price = $item->getPrice();
            $groupCode = $item->getGroupCode();
            $groupId = $this->hlpBusCodes->getMageIdForCustomerGroupByCode($groupCode);
            $result[] = $groupId;
            /* don't save prices for unknown groups */
            if (!is_null($groupId)) {
                $data->setCustomerGroupRef($groupId);
                $data->setPrice($price);
                $this->repoGroupPrice->create($data);
            }
        }
        return $result;
    }
}