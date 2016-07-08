<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Data\Agg\Lot as AggLot;
use Praxigento\Odoo\Data\Agg\Warehouse as AggWarehouse;
use Praxigento\Odoo\Data\Odoo\Inventory\Lot as ApiLot;
use Praxigento\Odoo\Data\Odoo\Inventory\Warehouse as ApiWarehouse;
use Praxigento\Odoo\Repo\Agg\ILot as IRepoAggLot;
use Praxigento\Odoo\Repo\Agg\IWarehouse as IRepoAggWarehouse;
use Praxigento\Odoo\Repo\IPv as IRepoPv;
use Praxigento\Odoo\Repo\IRegistry;

class Replicator
{
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var  IRepoAggLot */
    protected $_repoAggLot;
    /** @var  IRepoAggWarehouse */
    protected $_repoAggWrhs;
    /** @var  IRepoPv */
    protected $_repoPv;
    /** @var IRegistry */
    protected $_repoRegistry;
    /** @var Replicator\Product\Category */
    protected $_subProdCategory;
    /** @var Replicator\Product\Warehouse */
    protected $_subProdWarehouse;
    /** @var Replicator\Product */
    protected $_subProduct;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Repo\IRegistry $repoRegistry,
        \Praxigento\Odoo\Repo\Agg\ILot $repoAggLot,
        \Praxigento\Odoo\Repo\IPv $repoPv,
        \Praxigento\Odoo\Repo\Agg\IWarehouse $repoAggWrhs,
        Replicator\Product $subProduct,
        Replicator\Product\Category $subProdCategory,
        Replicator\Product\Warehouse $subProdWarehouse
    ) {
        $this->_manObj = $manObj;
        $this->_repoRegistry = $repoRegistry;
        $this->_repoAggLot = $repoAggLot;
        $this->_repoPv = $repoPv;
        $this->_repoAggWrhs = $repoAggWrhs;
        $this->_subProduct = $subProduct;
        $this->_subProdCategory = $subProdCategory;
        $this->_subProdWarehouse = $subProdWarehouse;
    }

    /**
     * @param ApiLot[] $lots
     * @throws \Exception
     */
    public function processLots($lots)
    {
        /** @var  $data AggLot */
        $data = $this->_manObj->create(AggLot::class);
        foreach ($lots as $item) {
            $data->setOdooId($item->getIdOdoo());
            $data->setCode($item->getNumber());
            $data->setExpDate($item->getExpirationDate());
            $lotExists = $this->_repoAggLot->getByOdooId($data->getOdooId());
            if (!$lotExists) {
                $this->_repoAggLot->create($data);
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
        $sku = $product->getSku();
        $name = $product->getName();
        $isActive = $product->getIsActive();
        $skipReplication = false; // skip replication for inactive products are missed in Mage
        $priceWholesale = $product->getPriceWholesale();
        $weight = $product->getWeight();
        $pvWholesale = $product->getPvWholesale();
        /* check does product item is already registered in Magento */
        if (!$this->_repoRegistry->isProductRegisteredInMage($idOdoo)) {
            if ($isActive) {
                /* create new product in Magento */
                $idMage = $this->_subProduct->create($sku, $name, $isActive, $priceWholesale, $pvWholesale, $weight);

                $this->_repoRegistry->registerProduct($idMage, $idOdoo);
                $this->_repoPv->registerProductWholesalePv($idMage, $pvWholesale);
            } else {
                /* skip product replication for not active and not existing products */
                $skipReplication = true;
            }
        } else {
            /* update attributes for magento product */
            $idMage = $this->_repoRegistry->getProductMageIdByOdooId($idOdoo);
            $this->_subProduct->update($idMage, $name, $isActive, $priceWholesale, $weight);
            $this->_repoPv->updateProductWholesalePv($idMage, $pvWholesale);
        }
        if (!$skipReplication) {
            /* check that categories are registered in Magento */
            $categories = $product->getCategories();
            $this->_subProdCategory->checkCategoriesExistence($categories);
            /* check product to categories links (add/remove) */
            $this->_subProdCategory->replicateCategories($idMage, $categories);
            /* update warehouse/lot/qty data  */
            $warehouses = $product->getWarehouses();
            $this->_subProdWarehouse->processWarehouses($idMage, $warehouses);
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
            $found = $this->_repoAggWrhs->getByOdooId($odooId);
            if (!$found) {
                /** @var  $aggData AggWarehouse */
                $aggData = $this->_manObj->create(AggWarehouse::class);
                $aggData->setOdooId($odooId);
                $aggData->setCurrency($item->getCurrency());
                $aggData->setWebsiteId(Cfg::DEF_WEBSITE_ID_ADMIN);
                $aggData->setCode($item->getCode());
                $aggData->setNote('replicated from Odoo');
                $created = $this->_repoAggWrhs->create($aggData);
                if (!$created->getId()) {
                    throw new \Exception('Cannot replicate warehouse.');
                }
            }
        }
    }
}