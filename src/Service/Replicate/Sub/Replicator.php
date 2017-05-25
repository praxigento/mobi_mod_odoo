<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub;

use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Repo\Agg\Data\Lot as AggLot;
use Praxigento\Odoo\Data\Odoo\Inventory\Lot as ApiLot;
use Praxigento\Odoo\Data\Odoo\Inventory\Warehouse as ApiWarehouse;
use Praxigento\Odoo\Repo\Agg\Store\ILot as IRepoAggLot;
use Praxigento\Odoo\Repo\Agg\IWarehouse as IRepoAggWarehouse;
use Praxigento\Odoo\Repo\IPv as IRepoPv;
use Praxigento\Odoo\Repo\IRegistry;

class Replicator
{
    /** @var  IRepoAggLot */
    protected $repoAggLot;
    /** @var  IRepoAggWarehouse */
    protected $repoAggWrhs;
    /** @var  IRepoPv */
    protected $repoPv;
    /** @var IRegistry */
    protected $repoRegistry;
    /** @var Replicator\Product\Category */
    protected $subProdCategory;
    /** @var Replicator\Product\Warehouse */
    protected $subProdWarehouse;
    /** @var Replicator\Product */
    protected $subProduct;

    public function __construct(
        \Praxigento\Odoo\Repo\IRegistry $repoRegistry,
        \Praxigento\Odoo\Repo\Agg\Store\ILot $repoAggLot,
        \Praxigento\Odoo\Repo\IPv $repoPv,
        \Praxigento\Odoo\Repo\Agg\IWarehouse $repoAggWrhs,
        Replicator\Product $subProduct,
        Replicator\Product\Category $subProdCategory,
        Replicator\Product\Warehouse $subProdWarehouse
    ) {
        $this->repoRegistry = $repoRegistry;
        $this->repoAggLot = $repoAggLot;
        $this->repoPv = $repoPv;
        $this->repoAggWrhs = $repoAggWrhs;
        $this->subProduct = $subProduct;
        $this->subProdCategory = $subProdCategory;
        $this->subProdWarehouse = $subProdWarehouse;
    }

    /**
     * @param ApiLot[] $lots
     * @throws \Exception
     */
    public function processLots($lots)
    {
        /** @var  $data AggLot */
        $data = new \Praxigento\Odoo\Repo\Agg\Data\Lot();
        foreach ($lots as $item) {
            $data->setOdooId($item->getIdOdoo());
            $data->setCode($item->getNumber());
            $data->setExpDate($item->getExpirationDate());
            $lotExists = $this->repoAggLot->getByOdooId($data->getOdooId());
            if (!$lotExists) {
                $this->repoAggLot->create($data);
            }
        }
    }

    /**
     * @param \Praxigento\Odoo\Data\Odoo\Inventory\Product $product
     */
    public function processProductItem($product)
    {
        assert($product instanceof \Praxigento\Odoo\Data\Odoo\Inventory\Product);
        $idOdoo = $product->getIdOdoo();
        $sku = trim($product->getSku());
        $name = trim($product->getName());
        $isActive = $product->getIsActive();
        $skipReplication = false; // skip replication for inactive products are missed in Mage
        $priceWholesale = $product->getPriceWholesale();
        $weight = $product->getWeight();
        $pvWholesale = $product->getPvWholesale();
        /* check does product item is already registered in Magento */
        if (!$this->repoRegistry->isProductRegisteredInMage($idOdoo)) {
            if ($isActive) {
                /* create new product in Magento */
                $idMage = $this->subProduct->create($sku, $name, $isActive, $priceWholesale, $weight);
                $this->repoRegistry->registerProduct($idMage, $idOdoo);
                $this->repoPv->registerProductWholesalePv($idMage, $pvWholesale);
            } else {
                /* skip product replication for not active and not existing products */
                $skipReplication = true;
            }
        } else {
            /* update attributes for magento product */
            $idMage = $this->repoRegistry->getProductMageIdByOdooId($idOdoo);
            $this->subProduct->update($idMage, $sku, $name, $isActive, $priceWholesale, $weight);
            $this->repoPv->updateProductWholesalePv($idMage, $pvWholesale);
        }
        if (!$skipReplication) {
            /* check that categories are registered in Magento */
            $categories = $product->getCategories();
            $this->subProdCategory->checkCategoriesExistence($categories);
            /* check product to categories links (add/remove) */
            $this->subProdCategory->replicateCategories($idMage, $categories);
            /* update warehouse/lot/qty data  */
            $warehouses = $product->getWarehouses();
            $this->subProdWarehouse->processWarehouses($idMage, $warehouses);
        }
    }

    /**
     * @param ApiWarehouse[] $warehouses
     * @throws \Exception
     */
    public function processWarehouses($warehouses)
    {
        foreach ($warehouses as $item) {
            $odooId = $item->getIdOdoo();
            $found = $this->repoAggWrhs->getByOdooId($odooId);
            if (!$found) {
                $aggData = new \Praxigento\Odoo\Repo\Agg\Data\Warehouse();
                $aggData->setOdooId($odooId);
                $aggData->setCurrency($item->getCurrency());
                $aggData->setWebsiteId(Cfg::DEF_WEBSITE_ID_ADMIN);
                $aggData->setCode($item->getCode());
                $aggData->setNote('replicated from Odoo');
                $created = $this->repoAggWrhs->create($aggData);
                if (!$created->getId()) {
                    throw new \Exception('Cannot replicate warehouse.');
                }
            }
        }
    }
}