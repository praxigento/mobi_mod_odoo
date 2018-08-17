<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler;

use Praxigento\Warehouse\Repo\Data\Group\Price as EWrhsGroupPrice;

class Price
{
    /** @var  array */
    private static $cacheCustomerGroupsIds;

    /** @var \Magento\Framework\Api\Search\SearchCriteriaFactory */
    private $factSearchCrit;
    /** @var \Praxigento\Odoo\Api\Helper\BusinessCodes */
    private $hlpBusCodes;
    /** @var \Magento\Customer\Api\GroupRepositoryInterface */
    private $daoCustGroup;
    /** @var \Praxigento\Warehouse\Repo\Dao\Group\Price */
    private $daoGroupPrice;

    public function __construct(
        \Magento\Framework\Api\Search\SearchCriteriaFactory $factSearchCrit,
        \Magento\Customer\Api\GroupRepositoryInterface $daoCustGroup,
        \Praxigento\Warehouse\Repo\Dao\Group\Price $daoGroupPrice,
        \Praxigento\Odoo\Api\Helper\BusinessCodes $hlpBusCodes
    ) {
        $this->factSearchCrit = $factSearchCrit;
        $this->daoCustGroup = $daoCustGroup;
        $this->daoGroupPrice = $daoGroupPrice;
        $this->hlpBusCodes = $hlpBusCodes;
    }

    /**
     * Remove all group prices for the stock item.
     *
     * @param int $stockItemId
     */
    private function cleanup($stockItemId)
    {
        $where = EWrhsGroupPrice::A_STOCK_ITEM_REF . '=' . (int)$stockItemId;
        $this->daoGroupPrice->delete($where);
    }

    /**
     * Process customer group prices for warehouses.
     *
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\GroupPrice\Item[] $prices
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
            $list = $this->daoCustGroup->getList($crit);
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
                $this->daoGroupPrice->create($data);
            }
        }
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\GroupPrice\Item[] $prices
     * @param int $stockItemId
     * @return int[] IDs for processed customer groups.
     */
    private function saveReceived($prices, $stockItemId)
    {
        $result = [];
        /* save new prices */
        $data = new EWrhsGroupPrice();
        $data->setStockItemRef($stockItemId);
        /** @var \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\GroupPrice\Item $item */
        foreach ($prices as $item) {
            $price = $item->getPrice();
            $groupCode = $item->getGroupCode();
            $groupId = $this->hlpBusCodes->getMageIdForCustomerGroupByCode($groupCode);
            $result[] = $groupId;
            /* don't save prices for unknown groups */
            if (!is_null($groupId)) {
                $data->setCustomerGroupRef($groupId);
                $data->setPrice($price);
                $this->daoGroupPrice->create($data);
            }
        }
        return $result;
    }
}