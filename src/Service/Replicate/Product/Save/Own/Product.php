<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own;

use Magento\Catalog\Model\Product\Attribute\Source\Status as Status;
use Magento\Catalog\Model\Product\Type as Type;
use Praxigento\Odoo\Data\Odoo\Inventory\Product as DProduct;

/**
 * Replicate products data into Magento (sub-service for the parent service).
 */
class Product
{
    private $cacheDefWrhs;
    /** @var \Magento\Catalog\Model\ProductFactory */
    private $factProd;
    /** @var \Magento\Framework\Api\Search\SearchCriteriaFactory */
    private $factSearchCrit;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    private $repoAttrSet;
    /** @var \Praxigento\Odoo\Repo\Entity\Product */
    private $repoOdooProd;
    /** @var \Praxigento\Odoo\Repo\Entity\Warehouse */
    private $repoOdooWrhs;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $repoProd;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface $repoAttrSet,
        \Praxigento\Odoo\Repo\Entity\Product $repoOdooProd,
        \Praxigento\Odoo\Repo\Entity\Warehouse $repoOdooWrhs,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Magento\Framework\Api\Search\SearchCriteriaFactory $factSearchCrit,
        \Magento\Catalog\Model\ProductFactory $factProd
    ) {
        $this->logger = $logger;
        $this->repoAttrSet = $repoAttrSet;
        $this->repoProd = $repoProd;
        $this->repoOdooProd = $repoOdooProd;
        $this->repoOdooWrhs = $repoOdooWrhs;
        $this->hlpStock = $hlpStock;
        $this->factSearchCrit = $factSearchCrit;
        $this->factProd = $factProd;
    }

    /**
     * Create new product in Magento.
     *
     * @param string $sku
     * @param string $name
     * @param bool $isActive
     * @param float $priceRetail
     * @param float $weight
     * @return int Magento ID for the new product.
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function create($sku, $name, $isActive, $priceRetail, $weight)
    {
        $this->logger->debug("Create new product (sku: $sku; name: $name; active: $isActive; weight: $weight.)");
        /**
         * Retrieve attribute set ID.
         */
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $crit */
        $crit = $this->factSearchCrit->create();
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
        $list = $this->repoAttrSet->getList($crit);
        $items = $list->getItems();
        $attrSet = reset($items);
        $attrSetId = $attrSet->getId();
        /**
         * Create simple product.
         */
        /** @var  $product \Magento\Catalog\Api\Data\ProductInterface */
        $product = $this->factProd->create();
        $product->setSku(trim($sku));
        $product->setName(trim($name));
        $status = $this->getStatus($isActive);
        $product->setStatus($status);
        $product->setPrice($priceRetail);
        $product->setWeight($weight);
        $product->setAttributeSetId($attrSetId);
        $product->setTypeId(Type::TYPE_SIMPLE);
        $product->setUrlKey($sku); // MOBI-331 : use SKU as URL Key instead of Product Name
        $saved = $this->repoProd->save($product);
        /* return product ID */
        $result = $saved->getId();
        return $result;
    }

    /**
     * @param DProduct $product
     * @throws \Exception
     */
    public function execute($product)
    {
        assert($product instanceof \Praxigento\Odoo\Data\Odoo\Inventory\Product);
        $idOdoo = $product->getIdOdoo();
        $sku = trim($product->getSku());
        $name = trim($product->getName());
        $isActive = $product->getIsActive();
        $skipReplication = false; // skip replication for inactive products are missed in Mage
        $weight = $product->getWeight();
        $pvWholesale = $product->getPvWholesale();
        $priceRetail = $this->getRetailPrice($product);
        /* check does product item is already registered in Magento */
        $found = $this->repoOdooProd->getByOdooId($idOdoo);
        if (!$found) {
            if ($isActive) {
                /* create new product in Magento */
                $idMage = $this->create($sku, $name, $isActive, $priceRetail, $weight);
                $this->repoRegistry->registerProduct($idMage, $idOdoo);
                $this->repoPv->registerProductWholesalePv($idMage, $pvWholesale);
            } else {
                /* skip product replication for not active and not existing products */
                $skipReplication = true;
            }
        } else {
            /* update attributes for magento product */
            $idMage = $this->repoRegistry->getProductMageIdByOdooId($idOdoo);
            $this->subProduct->update($idMage, $sku, $name, $isActive, $priceRetail, $weight);
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
     * Get default stock (warehouse), load warehouse data, cache and return Odoo ID for default warehouse.
     */
    protected function getDefWrhs()
    {
        if (is_null($this->cacheDefWrhs)) {
            $stockId = $this->hlpStock->getDefaultStockId();
            $wrhs = $this->repoOdooWrhs->getById($stockId);
            $this->cacheDefWrhs = $wrhs->getOdooRef();
        }
        return $this->cacheDefWrhs;
    }

    /**
     * MOBI-765: Extract warehouse price for default warehouse or use 1000 of money if missed.
     *
     * @return float
     */
    private function getRetailPrice(\Praxigento\Odoo\Data\Odoo\Inventory\Product $product)
    {
        $result = 1000;
        $wrhsTargetId = $this->getDefWrhs();
        $warehouses = $product->getWarehouses();
        foreach ($warehouses as $warehouse) {
            $wrhsCurrentId = $warehouse->getIdOdoo();
            if ($wrhsTargetId == $wrhsCurrentId) {
                $result = $warehouse->getPriceWarehouse();
                break;
            }
        }
        return $result;
    }

    /**
     * @param bool $isActive
     * @return int
     */
    private function getStatus($isActive)
    {
        $result = ($isActive) ? Status::STATUS_ENABLED : Status::STATUS_DISABLED;
        return $result;
    }
}