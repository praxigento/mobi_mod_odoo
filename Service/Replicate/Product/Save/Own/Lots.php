<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own;

use Praxigento\Odoo\Data\Odoo\Inventory\Lot as DLot;
use Praxigento\Odoo\Repo\Entity\Data\Lot as EOdooLot;
use Praxigento\Warehouse\Repo\Entity\Data\Lot as EWrhsLot;

/**
 * Check Odoo lots existence in Magento (sub-service for the parent service).
 */
class Lots
{
    /** @var \Praxigento\Odoo\Repo\Entity\Lot */
    private $repoOdooLot;
    /** @var \Praxigento\Warehouse\Repo\Entity\Lot */
    private $repoWrhsLot;

    public function __construct(
        \Praxigento\Odoo\Repo\Entity\Lot $repoOdooLot,
        \Praxigento\Warehouse\Repo\Entity\Lot $repoWrhsLot
    ) {
        $this->repoOdooLot = $repoOdooLot;
        $this->repoWrhsLot = $repoWrhsLot;
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
                $found = $this->repoOdooLot->getByOdooId($odooId);
                if (!$found) {
                    $wrhsData = new EWrhsLot();
                    $wrhsData->setCode($code);
                    $wrhsData->setExpDate($dateExp);
                    $lotId = $this->repoWrhsLot->create($wrhsData);
                    $odooData = new EOdooLot();
                    $odooData->setOdooRef($odooId);
                    $odooData->setMageRef($lotId);
                    $this->repoOdooLot->create($odooData);
                }
            }
        }
    }
}