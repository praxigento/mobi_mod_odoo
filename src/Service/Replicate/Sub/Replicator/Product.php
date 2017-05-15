<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;

class Product
{
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var   \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    protected $repoAttrSet;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $repoProd;

    public function __construct(
        \Praxigento\Core\Fw\Logger\App $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface $repoAttrSet,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd

    ) {
        $this->logger = $logger;
        $this->manObj = $manObj;
        $this->repoAttrSet = $repoAttrSet;
        $this->repoProd = $repoProd;
    }

    /**
     * @param bool $isActive
     * @return int
     */
    private function _getStatus($isActive)
    {
        $result = ($isActive) ? Status::STATUS_ENABLED : Status::STATUS_DISABLED;
        return $result;
    }

    /**
     * Create simple product.
     *
     * @param string $sku
     * @param string $name
     * @param bool $isActive
     * @param double $priceWholesale
     * @param double $weight
     * @return int
     */
    public function create($sku, $name, $isActive, $priceWholesale, $weight)
    {
        $this->logger->debug("Create new product (sku: $sku; name: $name; active: $isActive; price: $priceWholesale; weight: $weight.)");
        /**
         * Retrieve attribute set ID.
         */
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $crit */
        $crit = $this->manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
        $list = $this->repoAttrSet->getList($crit);
        $items = $list->getItems();
        $attrSet = reset($items);
        $attrSetId = $attrSet->getId();
        /**
         * Create simple product.
         */
        /** @var  $product ProductInterface */
        $product = $this->manObj->create(ProductInterface::class);
        $product->setSku(trim($sku));
        $product->setName(trim($name));
        $status = $this->_getStatus($isActive);
        $product->setStatus($status);
        $product->setPrice($priceWholesale);
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
     * Update simple product.
     *
     * @param int $mageId
     * @param string $sku
     * @param string $name
     * @param bool $isActive
     * @param double $priceWholesale
     * @param double $weight
     */
    public function update($mageId, $sku, $name, $isActive, $priceWholesale, $weight)
    {
        $this->logger->debug("Update product (id: $mageId; name: $name; active: $isActive; price: $priceWholesale; weight: $weight.)");
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->repoProd->getById($mageId);
        /* MOBI-717: SKU also can be changed */
        $product->setSku($sku);
        $product->setUrlKey($sku);
        $product->setName($name);
        $status = $this->_getStatus($isActive);
        $product->setStatus($status);
        $product->setPrice($priceWholesale);
        $product->setWeight($weight);
        $this->repoProd->save($product);
    }
}