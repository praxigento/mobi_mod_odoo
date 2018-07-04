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
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Category */
    private $ownCat;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse */
    private $ownWrhs;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    private $daoAttrSet;
    /** @var \Praxigento\Odoo\Repo\Dao\Product */
    private $daoOdooProd;
    /** @var \Praxigento\Odoo\Repo\Dao\Warehouse */
    private $daoOdooWrhs;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $daoProd;
    /** @var \Praxigento\Pv\Repo\Dao\Product */
    private $daoPvProd;

    public function __construct(
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface $daoAttrSet,
        \Praxigento\Odoo\Repo\Dao\Product $daoOdooProd,
        \Praxigento\Odoo\Repo\Dao\Warehouse $daoOdooWrhs,
        \Magento\Catalog\Api\ProductRepositoryInterface $daoProd,
        \Praxigento\Pv\Repo\Dao\Product $daoPvProd,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Magento\Framework\Api\Search\SearchCriteriaFactory $factSearchCrit,
        \Magento\Catalog\Model\ProductFactory $factProd,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Category $ownCat,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product\Warehouse $ownWrhs
    ) {
        $this->logger = $logger;
        $this->daoAttrSet = $daoAttrSet;
        $this->daoProd = $daoProd;
        $this->daoOdooProd = $daoOdooProd;
        $this->daoOdooWrhs = $daoOdooWrhs;
        $this->daoPvProd = $daoPvProd;
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
        $list = $this->daoAttrSet->getList($crit);
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
        $saved = $this->daoProd->save($product);
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
        $idMage = $this->daoOdooProd->getMageIdByOdooId($idOdoo);
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
            $this->ownCat->exec($idMage, $sku, $categories);
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
            $wrhs = $this->daoOdooWrhs->getById($stockId);
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
     * Create or update link between Magento & Odoo representation of the product.
     *
     * @param int $mageId
     * @param int $odooId
     */
    private function registerOdooProd($mageId, $odooId)
    {
        /** @var \Praxigento\Odoo\Repo\Data\Product $found */
        $found = $this->daoOdooProd->getById($mageId);
        if ($found) {
            /* update link */
            $odooIdSaved = $found->getOdooRef();
            if ($odooIdSaved != $odooId) {
                $found->setOdooRef($odooId);
                $this->daoOdooProd->updateById($mageId, $found);
            }
        } else {
            /* create new link */
            $entity = new \Praxigento\Odoo\Repo\Data\Product();
            $entity->setMageRef($mageId);
            $entity->setOdooRef($odooId);
            $this->daoOdooProd->create($entity);
        }
    }

    /**
     * Save Wholesale PV value for the product.
     *
     * @param int $prodId
     * @param float $pv
     */
    private function registerPvWholesale($prodId, $pv)
    {
        $found = $this->daoPvProd->getById($prodId);
        if ($found) {
            /* update data */
            $pvSaved = $found->getPv();
            if ($pvSaved != $pv) {
                $found->setPv($pv);
                $this->daoPvProd->updateById($prodId, $found);
            }
        } else {
            /* create new entry */
            $entity = new \Praxigento\Pv\Repo\Data\Product();
            $entity->setProductRef($prodId, $pv);
            $this->daoPvProd->create($entity);
        }
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
        $product = $this->daoProd->getById($mageId);
        /* MOBI-717: SKU also can be changed */
        $product->setSku($sku);
        $product->setUrlKey($sku);
        $product->setName($name);
        $status = $this->getStatus($isActive);
        $product->setStatus($status);
        $product->setPrice($priceRetail);
        $product->setWeight($weight);
        $this->daoProd->save($product);
    }

    private function updatePvWholesale($prodMageId, $pv)
    {

        $bind = [
            \Praxigento\Pv\Repo\Data\Product::A_PROD_REF => $prodMageId,
            \Praxigento\Pv\Repo\Data\Product::A_PV => $pv
        ];
        $where = \Praxigento\Pv\Repo\Data\Product::A_PROD_REF . '=' . (int)$prodMageId;
        $this->daoPvProd->update($bind, $where);
    }
}