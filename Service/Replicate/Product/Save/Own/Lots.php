<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own;

use Praxigento\Odoo\Data\Odoo\Inventory\Lot as DLot;
use Praxigento\Odoo\Repo\Data\Lot as EOdooLot;
use Praxigento\Warehouse\Repo\Data\Lot as EWrhsLot;

/**
 * Check Odoo lots existence in Magento (sub-service for the parent service).
 */
class Lots
{
    /** @var \Praxigento\Odoo\Repo\Dao\Lot */
    private $daoOdooLot;
    /** @var \Praxigento\Warehouse\Repo\Dao\Lot */
    private $daoWrhsLot;

    public function __construct(
        \Praxigento\Odoo\Repo\Dao\Lot $daoOdooLot,
        \Praxigento\Warehouse\Repo\Dao\Lot $daoWrhsLot
    ) {
        $this->daoOdooLot = $daoOdooLot;
        $this->daoWrhsLot = $daoWrhsLot;
    }


    /**
     * @param DLot[] $lots
     * @throws \Exception
     */
    public function execute($lots)
    {
        if (is_array($lots)) {
            foreach ($lots as $item) {
                $odooId = $item->getIdOdoo();
                $code = $item->getNumber();
                $dateExp = $item->getExpirationDate();
                $found = $this->daoOdooLot->getByOdooId($odooId);
                if (!$found) {
                    $wrhsData = new EWrhsLot();
                    $wrhsData->setCode($code);
                    $wrhsData->setExpDate($dateExp);
                    $lotId = $this->daoWrhsLot->create($wrhsData);
                    $odooData = new EOdooLot();
                    $odooData->setOdooRef($odooId);
                    $odooData->setMageRef($lotId);
                    $this->daoOdooLot->create($odooData);
                }
            }
        }
    }
}