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
    /** @var int */
    private static $cacheDefWrhs;

    /** @var \Magento\Catalog\Model\ProductFactory */
    private $factProd;
    /** @var \Magento\Framework\Api\Search\SearchCriteriaFactory */
    private $factSearchCrit;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Category */
    private $ownCat;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse */
    private $ownWrhs;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    private $repoAttrSet;
    /** @var \Praxigento\Odoo\Repo\Entity\Product */
    private $repoOdooProd;
    /** @var \Praxigento\Odoo\Repo\Entity\Warehouse */
    private $repoOdooWrhs;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;
    /** @var \Praxigento\Pv\Repo\Entity\Product */
    private $repoPvProd;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface $repoAttrSet,
        \Praxigento\Odoo\Repo\Entity\Product $repoOdooProd,
        \Praxigento\Odoo\Repo\Entity\Warehouse $repoOdooWrhs,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Praxigento\Pv\Repo\Entity\Product $repoPvProd,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Magento\Framework\Api\Search\SearchCriteriaFactory $factSearchCrit,
        \Magento\Catalog\Model\ProductFactory $factProd,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Category $ownCat,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse $ownWrhs
    ) {
        $this->logger = $logger;
        $this->repoAttrSet = $repoAttrSet;
        $this->repoProd = $repoProd;
        $this->repoOdooProd = $repoOdooProd;
        $this->repoOdooWrhs = $repoOdooWrhs;
        $this->repoPvProd = $repoPvProd;
        $this->hlpStock = $hlpStock;
        $this->factSearchCrit = $factSearchCrit;
        $this->factProd = $factProd;
        $this->ownCat = $ownCat;
        $this->ownWrhs = $ownWrhs;
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
        $isMissedAndInactive = false; // skip replication for inactive products are missed in Mage
        $weight = $product->getWeight();
        $pvWholesale = $product->getPvWholesale();
        $priceRetail = $this->getRetailPrice($product);
        /* check does product item is already registered in Magento */
        $idMage = $this->repoOdooProd->getMageIdByOdooId($idOdoo);
        if (!$idMage) {
            if ($isActive) {
                /* create new product in Magento */
                $idMage = $this->create($sku, $name, $isActive, $priceRetail, $weight);
                $this->registerOdooProd($idMage, $idOdoo);
                $this->registerPvWholesale($idMage, $pvWholesale);
            } else {
                /* skip product replication for not active and not existing products */
                $isMissedAndInactive = true;
            }
        } else {
            /* update attributes for magento product */
            $this->update($idMage, $sku, $name, $isActive, $priceRetail, $weight);
            $this->updatePvWholesale($idMage, $pvWholesale);
        }
        if (!$isMissedAndInactive) {
            /* replicate Odoo categories into Magento */
            $categories = $product->getCategories();
            $this->ownCat->exec($idMage, $categories);
            /* replicate warehouse/lot/qty data  */
            $warehouses = $product->getWarehouses();
            $this->ownWrhs->exec($idMage, $warehouses);
        }
    }

    /**
     * Get default stock (warehouse), load warehouse data, cache and return Odoo ID for default warehouse.
     */
    private function getOdooIdForDefWarehouse()
    {
        if (is_null(self::$cacheDefWrhs)) {
            $stockId = $this->hlpStock->getDefaultStockId();
            $wrhs = $this->repoOdooWrhs->getById($stockId);
            self::$cacheDefWrhs = $wrhs->getOdooRef();
        }
        return self::$cacheDefWrhs;
    }

    /**
     * MOBI-765: Extract warehouse price for default warehouse or use 1000 of money if missed.
     *
     * @return float
     */
    private function getRetailPrice(\Praxigento\Odoo\Data\Odoo\Inventory\Product $product)
    {
        $result = 1000;
        $wrhsTargetId = $this->getOdooIdForDefWarehouse();
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

    /**
     * Create link between Magento & Odoo representation of the product.
     *
     * @param int $mageId
     * @param int $odooId
     */
    private function registerOdooProd($mageId, $odooId)
    {
        $entity = new \Praxigento\Odoo\Repo\Entity\Data\Product();
        $entity->setMageRef($mageId);
        $entity->setOdooRef($odooId);
        $this->repoOdooProd->create($entity);
    }

    /**
     * Save Wholesale PV value for the product.
     *
     * @param int $prodId
     * @param float $pv
     */
    private function registerPvWholesale($prodId, $pv)
    {
        $entity = new \Praxigento\Pv\Repo\Entity\Data\Product();
        $entity->setProductRef($prodId, $pv);
        $this->repoPvProd->create($entity);
    }

    /**
     * @param int $mageId
     * @param string $sku
     * @param string $name
     * @param bool $isActive
     * @param float $priceRetail
     * @param float $weight
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function update($mageId, $sku, $name, $isActive, $priceRetail, $weight)
    {
        $this->logger->debug("Update product (id: $mageId; name: $name; active: $isActive; weight: $weight.)");
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->repoProd->getById($mageId);
        /* MOBI-717: SKU also can be changed */
        $product->setSku($sku);
        $product->setUrlKey($sku);
        $product->setName($name);
        $status = $this->getStatus($isActive);
        $product->setStatus($status);
        $product->setPrice($priceRetail);
        $product->setWeight($weight);
        $this->repoProd->save($product);
    }

    private function updatePvWholesale($prodMageId, $pv)
    {

        $bind = [
            \Praxigento\Pv\Repo\Entity\Data\Product::ATTR_PROD_REF => $prodMageId,
            \Praxigento\Pv\Repo\Entity\Data\Product::ATTR_PV => $pv
        ];
        $where = \Praxigento\Pv\Repo\Entity\Data\Product::ATTR_PROD_REF . '=' . (int)$prodMageId;
        $this->repoPvProd->update($bind, $where);
    }
}