<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface as IProductRepo;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;

class Product
{
    /** @var \Psr\Log\LoggerInterface */
    protected $_logger;
    /** @var \Magento\Catalog\Api\AttributeSetRepositoryInterface */
    protected $_mageRepoAttrSet;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $_mageRepoProd;
    /** @var   \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\AttributeSetRepositoryInterface $mageRepoAttrSet,
        \Magento\Catalog\Api\ProductRepositoryInterface $mageRepoProd

    ) {
        $this->_manObj = $manObj;
        $this->_logger = $logger;
        $this->_mageRepoAttrSet = $mageRepoAttrSet;
        $this->_mageRepoProd = $mageRepoProd;
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
        $this->_logger->debug("Create new product (sku: $sku; name: $name; active: $isActive; price: $priceWholesale; weight: $weight.)");
        if ($sku == '1835MY' || $sku == '2078AU' || $sku == '2361JP') {
            $name = $sku . ' - ' . $name;
        }
        /**
         * Retrieve attribute set ID.
         */
        /** @var \Magento\Framework\Api\SearchCriteriaInterface $crit */
        $crit = $this->_manObj->create(\Magento\Framework\Api\SearchCriteriaInterface::class);
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attrSet */
        $list = $this->_mageRepoAttrSet->getList($crit);
        $items = $list->getItems();
        $attrSet = reset($items);
        $attrSetId = $attrSet->getId();
        /**
         * Create simple product.
         */
        /** @var  $product ProductInterface */
        $product = $this->_manObj->create(ProductInterface::class);
        $product->setSku($sku);
        $product->setName($name);
        $status = $this->_getStatus($isActive);
        $product->setStatus($status);
        $product->setPrice($priceWholesale);
        $product->setWeight($weight);
        $product->setAttributeSetId($attrSetId);
        $product->setTypeId(Type::TYPE_SIMPLE);
        $saved = $this->_mageRepoProd->save($product);
        /* return product ID */
        $result = $saved->getId();
        return $result;
    }

    /**
     * Create simple product.
     *
     * @param int $mageId
     * @param string $name
     * @param bool $isActive
     * @param double $priceWholesale
     * @param double $weight
     */
    public function update($mageId, $name, $isActive, $priceWholesale, $weight)
    {
        $this->_logger->debug("Update product (id: $mageId; name: $name; active: $isActive; price: $priceWholesale; weight: $weight.)");
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->_mageRepoProd->getById($mageId);
        // SKU should not be changed
        $product->setName($name);
        $status = $this->_getStatus($isActive);
        $product->setStatus($status);
        $product->setPrice($priceWholesale);
        $product->setWeight($weight);
        $this->_mageRepoProd->save($product);
    }
}