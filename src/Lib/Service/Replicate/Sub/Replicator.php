<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Lib\Service\Replicate\Sub;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Config as Cfg;
use Praxigento\Odoo\Api\Data\Bundle\ILot as ApiLot;
use Praxigento\Odoo\Api\Data\Bundle\IWarehouse as ApiWarehouse;
use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Odoo\Repo\Agg\IWarehouse as IRepoWarehouse;
use Praxigento\Odoo\Lib\Repo\ILot as IRepoLot;

class Replicator
{
    /** @var   ObjectManagerInterface */
    private $_manObj;
    /** @var  IRepoLot */
    private $_repoLot;
    /** @var  IRepoWarehouse */
    private $_repoWrhs;

    public function __construct(
        ObjectManagerInterface $manObj,
        IRepoWarehouse $repoWrhs,
        IRepoLot $repoLot
    ) {
        $this->_manObj = $manObj;
        $this->_repoWrhs = $repoWrhs;
        $this->_repoLot = $repoLot;
    }

    /**
     * @param ApiLot[] $lots
     * @throws \Exception
     */
    public function processLots($lots)
    {
        /** @var  $aggData AggLot */
        $aggData = $this->_manObj->create(AggLot::class);
        foreach ($lots as $odooId => $lot) {
            $aggData->setOdooId($odooId);
            $aggData->setCode($lot->getCode());
            $aggData->setExpDate($lot->getExpirationDate());
            $this->_repoLot->checkExistence($aggData);
        }
    }

    /**
     * @param ApiWarehouse[] $warehouses
     * @throws \Exception
     */
    public function processWarehouses($warehouses)
    {
        foreach ($warehouses as $odooId => $wrhs) {
            $found = $this->_repoWrhs->getByOdooId($odooId);
            if (!$found) {
                /** @var  $aggData AggWarehouse */
                $aggData = $this->_manObj->create(AggWarehouse::class);
                $aggData->setOdooId($odooId);
                $aggData->setCurrency($wrhs->getCurrency());
                $aggData->setWebsiteId(Cfg::DEF_WEBSITE_ID_BASE);
                $aggData->setCode($wrhs->getCode());
                $aggData->setNote('replicated from Odoo');
                $created = $this->_repoWrhs->create($aggData);
                if (!$created->getId()) {
                    throw new \Exception('Cannot replicate warehouse.');
                }
            }
        }
    }
}