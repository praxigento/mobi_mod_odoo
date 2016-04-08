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
use Praxigento\Odoo\Data\Entity\Product as EntityProduct;
use Praxigento\Odoo\Lib\Repo\ILot as IRepoModLot;
use Praxigento\Odoo\Repo\Agg\IWarehouse as IRepoModWarehouse;
use Praxigento\Odoo\Repo\IModule;
use Praxigento\Odoo\Repo\IPvModule as IRepoModPv;

class Replicator
{
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var IModule */
    protected $_repoMod;
    /** @var  IRepoModLot */
    protected $_repoModLot;
    /** @var  IRepoModPv */
    protected $_repoModPv;
    /** @var  IRepoModWarehouse */
    protected $_repoModWrhs;
    /** @var Replicator\Product */
    protected $_subProduct;

    public function __construct(
        ObjectManagerInterface $manObj,
        IModule $repoMod,
        IRepoModLot $repoModLot,
        IRepoModPv $repoModPv,
        IRepoModWarehouse $repoModWrhs,
        Replicator\Product $subProduct
    ) {
        $this->_manObj = $manObj;
        $this->_repoMod = $repoMod;
        $this->_repoModLot = $repoModLot;
        $this->_repoModPv = $repoModPv;
        $this->_repoModWrhs = $repoModWrhs;
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
            $this->_repoModLot->checkExistence($aggData);
        }
    }

    /**
     * @param \Praxigento\Odoo\Api\Data\Bundle\IProduct $product
     */
    public function processProductItem($product)
    {
        assert($product instanceof \Praxigento\Odoo\Api\Data\Bundle\IProduct);
        $idOdoo = $product->getId();
        $sku = $product->getSku();
        $name = $product->getName();
        $priceWholesale = $product->getPrice();
        $weight = $product->getPrice();
        $pvWholesale = $product->getPv();
        $categories = $product->getCategories();
        /* check does product item is already registered in Magento */
        if (!$this->_repoMod->isOdooProductRegisteredInMage($idOdoo)) {
            /* create new product in Magento */
            $idMage = $this->_subProduct->create($sku, $name, $priceWholesale, $pvWholesale, $weight);
            $this->_repoMod->registerMageIdForOdooId(EntityProduct::ENTITY_NAME, $idMage, $idOdoo);
            $this->_repoModPv->saveProductWholesalePv($idMage, $pvWholesale);
        } else {
            /* update attributes for magento product */
            $idMage = $this->_repoMod->getMageIdByOdooId(EntityProduct::ENTITY_NAME, $idOdoo);
            $this->_subProduct->update($idMage, $sku, $name, $priceWholesale, $weight);
            $this->_repoModPv->updateProductWholesalePv($idMage, $pvWholesale);
        }
        /* replicate categories */

    }

    /**
     * @param ApiWarehouse[] $warehouses
     * @throws \Exception
     */
    public function processWarehouses($warehouses)
    {
        foreach ($warehouses as $item) {
            $odooId = $item->getId();
            $found = $this->_repoModWrhs->getByOdooId($odooId);
            if (!$found) {
                /** @var  $aggData AggWarehouse */
                $aggData = $this->_manObj->create(AggWarehouse::class);
                $aggData->setOdooId($odooId);
                $aggData->setCurrency($item->getCurrency());
                $aggData->setWebsiteId(Cfg::DEF_WEBSITE_ID_BASE);
                $aggData->setCode($item->getCode());
                $aggData->setNote('replicated from Odoo');
                $created = $this->_repoModWrhs->create($aggData);
                if (!$created->getId()) {
                    throw new \Exception('Cannot replicate warehouse.');
                }
            }
        }
    }
}