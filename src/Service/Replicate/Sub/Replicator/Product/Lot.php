<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

use Praxigento\Odoo\Data\Entity\Lot as EntityLot;
use Praxigento\Odoo\Repo\IRegistry;
use Praxigento\Warehouse\Repo\Entity\IQuantity as IRepoWarehouseEntityQuantity;

class Lot
{

    /** @var IRegistry */
    protected $_repoMod;
    /** @var  IRepoWarehouseEntityQuantity */
    protected $_repoWarehouseEntityQuantity;

    public function __construct(
        IRegistry $repoMod,
        IRepoWarehouseEntityQuantity $repoWarehouseEntityQuantity
    ) {
        $this->_repoMod = $repoMod; //
        $this->_repoWarehouseEntityQuantity = $repoWarehouseEntityQuantity;//
    }

    /**
     * Clean up extra lots for the stock item.
     *
     * @param int $stockItemId Magento ID for stock item.
     * @param \Praxigento\Odoo\Data\Api\Bundle\Product\Warehouse\ILot[] $lots list of the actual lots.
     */
    public function cleanupLots($stockItemId, $lots)
    {
        $ref = $this->_repoWarehouseEntityQuantity->getRef();
        $where = $ref::ATTR_STOCK_ITEM_REF . '=' . (int)$stockItemId;
        $lotsExist = $this->_repoWarehouseEntityQuantity->get($where);
        // create map of the Magento IDs for existing lots
        $mapMageExist = [];
        foreach ($lotsExist as $item) {
            $lotIdMage = $item[$ref::ATTR_LOT_REF];
            $mapMageExist[] = $lotIdMage;
        }
        // create map for Lots from request
        $mapOdooExist = [];
        foreach ($lots as $lot) {
            $lotIdOdoo = $lot->getId();
            $lotIdMage = $this->_repoMod->getLotMageIdByOdooId($lotIdOdoo);
            $mapOdooExist[] = $lotIdMage;
        }
        $diff = array_diff($mapMageExist, $mapOdooExist);
        foreach ($diff as $lotIdMage) {
            $pk = [$ref::ATTR_STOCK_ITEM_REF => $stockItemId, $ref::ATTR_LOT_REF => $lotIdMage];
            $this->_repoWarehouseEntityQuantity->deleteById($pk);
        }
    }

    /**
     * Process lot data (create or update quantities).
     *
     * @param int $stockItemId Magento ID for stock item related to the lot.
     * @param \Praxigento\Odoo\Data\Api\Bundle\Product\Warehouse\ILot $lot
     * @return double quantity of the product in the lot
     */
    public function processLot($stockItemId, $lot)
    {
        $refQty = $this->_repoWarehouseEntityQuantity->getRef();
        $lotIdOdoo = $lot->getId();
        $qty = $lot->getQuantity();
        $lotIdMage = $this->_repoMod->getLotMageIdByOdooId($lotIdOdoo);
        $pk = [$refQty::ATTR_STOCK_ITEM_REF => $stockItemId, $refQty::ATTR_LOT_REF => $lotIdMage];
        $qtyItem = $this->_repoWarehouseEntityQuantity->getById($pk);
        if ($qtyItem) {
            /* update lot qty data */
            $bind = [$refQty::ATTR_TOTAL => $qty];
            $this->_repoWarehouseEntityQuantity->updateById($bind, $pk);
        } else {
            /* create lot qty data */
            $pk[$refQty::ATTR_TOTAL] = $qty;
            $this->_repoWarehouseEntityQuantity->create($pk);
        }
        return $qty;
    }
}