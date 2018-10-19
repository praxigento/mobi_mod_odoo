<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse\Handler;

use Praxigento\Warehouse\Repo\Data\Quantity as EWrhsQty;

/**
 * Lots quantities handler.
 */
class Lot
{
    /** @var \Praxigento\Odoo\Repo\Dao\Lot */
    private $daoOdooLot;
    /** @var \Praxigento\Warehouse\Repo\Dao\Quantity */
    private $daoWrhsQty;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    public function __construct(
        \Praxigento\Odoo\App\Logger\Main $logger,
        \Praxigento\Odoo\Repo\Dao\Lot $daoOdooLot,
        \Praxigento\Warehouse\Repo\Dao\Quantity $daoWrhsQty
    ) {
        $this->logger = $logger;
        $this->daoOdooLot = $daoOdooLot;
        $this->daoWrhsQty = $daoWrhsQty;
    }

    /**
     * Clean up extra lots for the stock item.
     *
     * @param int $stockItemId Magento ID for stock item.
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\Lot[] $lots list of the actual lots.
     */
    public function cleanup($stockItemId, $lots)
    {
        /* create map for Lots from request */
        $mapOdooExist = $this->mapLotsOdoo($lots);
        /* create map of the Magento IDs for existing lots */
        $lotsExist = $this->daoWrhsQty->getByStockItemId($stockItemId);
        $mapMageExist = $this->mapLotsMage($lotsExist);
        /* remove Magento lots that have no Odoo correspondents */
        $diff = array_diff($mapMageExist, $mapOdooExist);
        foreach ($diff as $lotIdMage) {
            $pk = [
                EWrhsQty::A_STOCK_ITEM_REF => $stockItemId,
                EWrhsQty::A_LOT_REF => $lotIdMage
            ];
            $this->daoWrhsQty->deleteById($pk);
            $this->logger->info("Empty lot is removed - lot: $lotIdMage, stockItem: $stockItemId;");
        }
    }

    /**
     * Convert Magento entities array into lots IDs array.
     *
     * @param \Praxigento\Warehouse\Repo\Data\Quantity[] $lots
     * @return int[]
     */
    private function mapLotsMage($lots)
    {
        $result = [];
        /** @var \Praxigento\Warehouse\Repo\Data\Quantity $item */
        foreach ($lots as $item) {
            $lotIdMage = $item->getLotRef();
            $result[] = $lotIdMage;
        }
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\Lot[] $lots
     * @return int[]
     */
    private function mapLotsOdoo($lots)
    {
        $result = [];
        /** @var \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\Lot $lot */
        foreach ($lots as $lot) {
            $lotIdOdoo = $lot->getIdOdoo();
            $lotIdMage = $this->daoOdooLot->getMageIdByOdooId($lotIdOdoo);
            $result[] = $lotIdMage;
        }
        return $result;
    }

    /**
     * Save lot data (create or update quantities).
     *
     * @param int $stockItemId Magento ID for stock item related to the lot.
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\Lot $lot Odoo data.
     * @return float quantity of the product in the lot
     */
    public function save($stockItemId, $lot)
    {
        $lotIdOdoo = $lot->getIdOdoo();
        $qty = $lot->getQuantity();
        $lotIdMage = $this->daoOdooLot->getMageIdByOdooId($lotIdOdoo);
        $pk = [
            EWrhsQty::A_STOCK_ITEM_REF => $stockItemId,
            EWrhsQty::A_LOT_REF => $lotIdMage
        ];
        /* get quantity item (total product qty for lot on the stock) */
        $qtyItem = $this->daoWrhsQty->getById($pk);
        if ($qtyItem) {
            /* update qty data */
            $bind = [EWrhsQty::A_TOTAL => $qty];
            $this->daoWrhsQty->updateById($pk, $bind);
        } else {
            /* create qty entity based on primary key data */
            $entity = new EWrhsQty($pk);
            $entity->setTotal($qty);
            $this->daoWrhsQty->create($entity);
        }
        return $qty;
    }
}