<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

use Praxigento\Warehouse\Data\Entity\Quantity as EntityWarehouseQuantity;

class Lot
{

    /** @var \Praxigento\Odoo\Repo\Agg\ILot */
    protected $_repoAggLot;
    /** @var  \Praxigento\Warehouse\Repo\Entity\IQuantity */
    protected $_repoWarehouseEntityQuantity;

    public function __construct(
        \Praxigento\Odoo\Repo\Agg\ILot $repoAggLot,
        \Praxigento\Warehouse\Repo\Entity\IQuantity $repoWarehouseEntityQuantity
    ) {
        $this->_repoAggLot = $repoAggLot;
        $this->_repoWarehouseEntityQuantity = $repoWarehouseEntityQuantity;
    }

    /**
     * Clean up extra lots for the stock item.
     *
     * @param int $stockItemId Magento ID for stock item.
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\Lot[] $lots list of the actual lots.
     */
    public function cleanupLots($stockItemId, $lots)
    {
        $where = EntityWarehouseQuantity::ATTR_STOCK_ITEM_REF . '=' . (int)$stockItemId;
        $lotsExist = $this->_repoWarehouseEntityQuantity->get($where);
        // create map of the Magento IDs for existing lots
        $mapMageExist = [];
        foreach ($lotsExist as $item) {
            $lotIdMage = $item[EntityWarehouseQuantity::ATTR_LOT_REF];
            $mapMageExist[] = $lotIdMage;
        }
        // create map for Lots from request
        $mapOdooExist = [];
        foreach ($lots as $lot) {
            $lotIdOdoo = $lot->getIdOdoo();
            $lotIdMage = $this->_repoAggLot->getMageIdByOdooId($lotIdOdoo);
            $mapOdooExist[] = $lotIdMage;
        }
        $diff = array_diff($mapMageExist, $mapOdooExist);
        foreach ($diff as $lotIdMage) {
            $pk = [
                EntityWarehouseQuantity::ATTR_STOCK_ITEM_REF => $stockItemId,
                EntityWarehouseQuantity::ATTR_LOT_REF => $lotIdMage
            ];
            $this->_repoWarehouseEntityQuantity->deleteById($pk);
        }
    }

    /**
     * Process lot data (create or update quantities).
     *
     * @param int $stockItemId Magento ID for stock item related to the lot.
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\Lot $lot
     * @return double quantity of the product in the lot
     */
    public function processLot($stockItemId, $lot)
    {
        $lotIdOdoo = $lot->getIdOdoo();
        $qty = $lot->getQuantity();
        $lotIdMage = $this->_repoAggLot->getMageIdByOdooId($lotIdOdoo);
        $pk = [
            EntityWarehouseQuantity::ATTR_STOCK_ITEM_REF => $stockItemId,
            EntityWarehouseQuantity::ATTR_LOT_REF => $lotIdMage
        ];
        $qtyItem = $this->_repoWarehouseEntityQuantity->getById($pk);
        if ($qtyItem) {
            /* update lot qty data */
            $bind = [EntityWarehouseQuantity::ATTR_TOTAL => $qty];
            $this->_repoWarehouseEntityQuantity->updateById($pk, $bind);
        } else {
            /* create lot qty data */
            $pk[EntityWarehouseQuantity::ATTR_TOTAL] = $qty;
            $this->_repoWarehouseEntityQuantity->create($pk);
        }
        return $qty;
    }
}