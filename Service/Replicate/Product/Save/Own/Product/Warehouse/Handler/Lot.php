<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler;

use Praxigento\Warehouse\Repo\Entity\Data\Quantity as EWrhsQty;

/**
 * Lots quantities handler.
 */
class Lot
{
    /** @var \Praxigento\Odoo\Repo\Entity\Lot */
    private $repoOdooLot;
    /** @var \Praxigento\Warehouse\Repo\Entity\Quantity */
    private $repoWrhsQty;

    public function __construct(
        \Praxigento\Odoo\Repo\Entity\Lot $repoOdooLot,
        \Praxigento\Warehouse\Repo\Entity\Quantity $repoWrhsQty
    ) {
        $this->repoOdooLot = $repoOdooLot;
        $this->repoWrhsQty = $repoWrhsQty;
    }

    /**
     * Clean up extra lots for the stock item.
     *
     * @param int $stockItemId Magento ID for stock item.
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\Lot[] $lots list of the actual lots.
     */
    public function cleanup($stockItemId, $lots)
    {
        /* create map for Lots from request */
        $mapOdooExist = $this->mapLotsOdoo($lots);
        /* create map of the Magento IDs for existing lots */
        $lotsExist = $this->repoWrhsQty->getByStockItemId($stockItemId);
        $mapMageExist = $this->mapLotsMage($lotsExist);
        /* remove Magento lots that have no Odoo correspondents */
        $diff = array_diff($mapMageExist, $mapOdooExist);
        foreach ($diff as $lotIdMage) {
            $pk = [
                EWrhsQty::ATTR_STOCK_ITEM_REF => $stockItemId,
                EWrhsQty::ATTR_LOT_REF => $lotIdMage
            ];
            $this->repoWrhsQty->deleteById($pk);
        }
    }

    /**
     * Convert Magento entities array into lots IDs array.
     *
     * @param \Praxigento\Warehouse\Repo\Entity\Data\Quantity[] $lots
     * @return int[]
     */
    private function mapLotsMage($lots)
    {
        $result = [];
        /** @var \Praxigento\Warehouse\Repo\Entity\Data\Quantity $item */
        foreach ($lots as $item) {
            $lotIdMage = $item->getLotRef();
            $result[] = $lotIdMage;
        }
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\Lot[] $lots
     * @return int[]
     */
    private function mapLotsOdoo($lots)
    {
        $result = [];
        /** @var \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\Lot $lot */
        foreach ($lots as $lot) {
            $lotIdOdoo = $lot->getIdOdoo();
            $lotIdMage = $this->repoOdooLot->getMageIdByOdooId($lotIdOdoo);
            $result[] = $lotIdMage;
        }
        return $result;
    }

    /**
     * Save lot data (create or update quantities).
     *
     * @param int $stockItemId Magento ID for stock item related to the lot.
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product\Warehouse\Lot $lot Odoo data.
     * @return float quantity of the product in the lot
     */
    public function save($stockItemId, $lot)
    {
        $lotIdOdoo = $lot->getIdOdoo();
        $qty = $lot->getQuantity();
        $lotIdMage = $this->repoOdooLot->getMageIdByOdooId($lotIdOdoo);
        $pk = [
            EWrhsQty::ATTR_STOCK_ITEM_REF => $stockItemId,
            EWrhsQty::ATTR_LOT_REF => $lotIdMage
        ];
        /* get quantity item (total product qty for lot on the stock) */
        $qtyItem = $this->repoWrhsQty->getById($pk);
        if ($qtyItem) {
            /* update qty data */
            $bind = [EWrhsQty::ATTR_TOTAL => $qty];
            $this->repoWrhsQty->updateById($pk, $bind);
        } else {
            /* create qty entity based on primary key data */
            $entity = new EWrhsQty($pk);
            $entity->setTotal($qty);
            $this->repoWrhsQty->create($entity);
        }
        return $qty;
    }
}