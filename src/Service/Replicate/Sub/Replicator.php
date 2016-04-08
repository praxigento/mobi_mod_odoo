<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\Config as Cfg;
use Praxigento\Odoo\Api\Data\Bundle\ICategory as ApiCategory;
use Praxigento\Odoo\Api\Data\Bundle\ILot as ApiLot;
use Praxigento\Odoo\Api\Data\Bundle\IWarehouse as ApiWarehouse;
use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Odoo\Lib\Repo\ILot as IRepoLot;
use Praxigento\Odoo\Repo\Agg\IWarehouse as IRepoWarehouse;
use Praxigento\Odoo\Repo\IModule;

class Replicator
{
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var  IRepoLot */
    protected $_repoLot;
    /** @var IModule */
    protected $_repoMod;
    /** @var  IRepoWarehouse */
    protected $_repoWrhs;
    /** @var Replicator\Product */
    protected $_subProduct;

    public function __construct(
        ObjectManagerInterface $manObj,
        IModule $repoMod,
        IRepoWarehouse $repoWrhs,
        IRepoLot $repoLot,
        Replicator\Product $subProduct
    ) {
        $this->_manObj = $manObj;
        $this->_repoMod = $repoMod;
        $this->_repoWrhs = $repoWrhs;
        $this->_repoLot = $repoLot;
        $this->_subProduct = $subProduct;
    }

    /**
     * We need to check all categories against the list of existing in Mage and create new categories
     * if they are absent in the list.
     *
     * @param ApiCategory[] $cats
     */
    public function processCategories($cats)
    {
        /** @var  $aggData AggLot */
        $aggData = $this->_manObj->create(AggCa::class);
        foreach ($cats as $item) {
//            $aggData->setOdooId($item->getId());
//            $aggData->setCode($item->getCode());
//            $aggData->setExpDate($item->getExpirationDate());
//            $this->_repoLot->checkExistence($aggData);
        }
    }

    /**
     * @param ApiLot[] $lots
     * @throws \Exception
     */
    public function processLots($lots)
    {
        /** @var  $aggData AggLot */
        $aggData = $this->_manObj->create(AggLot::class);
        foreach ($lots as $item) {
            $aggData->setOdooId($item->getId());
            $aggData->setCode($item->getCode());
            $aggData->setExpDate($item->getExpirationDate());
            $this->_repoLot->checkExistence($aggData);
        }
    }

    /**
     * @param \Praxigento\Odoo\Api\Data\Bundle\IProduct $product
     */
    public function processProductItem($product)
    {
        assert($product instanceof \Praxigento\Odoo\Api\Data\Bundle\IProduct);
        $idOdoo = $product->getId();
        /* check does product item is already registered in Magento */


        $sku = $product->getSku();
        $name = $product->getName();
        $price = $product->getPrice();
        $weight = $product->getPrice();
        $pv = $product->getPv();
        $mageId = $this->_subProduct->create($sku, $name, $price, $weight);
    }

    /**
     * @param ApiWarehouse[] $warehouses
     * @throws \Exception
     */
    public function processWarehouses($warehouses)
    {
        foreach ($warehouses as $item) {
            $odooId = $item->getId();
            $found = $this->_repoWrhs->getByOdooId($odooId);
            if (!$found) {
                /** @var  $aggData AggWarehouse */
                $aggData = $this->_manObj->create(AggWarehouse::class);
                $aggData->setOdooId($odooId);
                $aggData->setCurrency($item->getCurrency());
                $aggData->setWebsiteId(Cfg::DEF_WEBSITE_ID_BASE);
                $aggData->setCode($item->getCode());
                $aggData->setNote('replicated from Odoo');
                $created = $this->_repoWrhs->create($aggData);
                if (!$created->getId()) {
                    throw new \Exception('Cannot replicate warehouse.');
                }
            }
        }
    }
}