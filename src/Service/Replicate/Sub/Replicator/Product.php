<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator;

use Magento\Catalog\Api\ProductRepositoryInterface as IProductRepo;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\TypeFactory as EntityTypeFactory;
use Magento\Framework\ObjectManagerInterface;

class Product
{
    /** @var AttributeSetFactory */
    protected $_mageFactAttrSet;
    /** @var EntityTypeFactory */
    protected $_mageFactEntityType;
    /** @var IProductRepo */
    protected $_mageRepoProd;
    /** @var   ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        ObjectManagerInterface $manObj,
        EntityTypeFactory $mageFactEntityType,
        AttributeSetFactory $mageFfactAttrSet,
        IProductRepo $mageRepoProd
    ) {
        $this->_manObj = $manObj;
        $this->_mageFactEntityType = $mageFactEntityType;
        $this->_mageFactAttrSet = $mageFfactAttrSet;
        $this->_mageRepoProd = $mageRepoProd;
    }

    /**
     * Create simple product.
     *
     * @param string $sku
     * @param string $name
     * @param double $priceWholesale
     * @param double $weight
     * @return int
     */
    public function create($sku, $name, $priceWholesale, $weight)
    {
        /**
         * Retrieve entity type ID & attribute set ID.
         */
        /** @var  $entityType \Magento\Eav\Model\Entity\Type */
        $entityType = $this->_mageFactEntityType
            ->create()
            ->loadByCode(\Magento\Catalog\Model\Product::ENTITY);
        $entityTypeId = $entityType->getId();
        $attrSet = $this->_mageFactAttrSet
            ->create()
            ->load($entityTypeId, \Magento\Eav\Model\Entity\Attribute\Set::KEY_ENTITY_TYPE_ID);
        $attrSetId = $attrSet->getId();
        /**
         * Create simple product.
         */
        /** @var  $product \Magento\Catalog\Api\Data\ProductInterface */
        $product = $this->_manObj->create(\Magento\Catalog\Api\Data\ProductInterface::class);
        $product->setSku($sku);
        $product->setName($name);
        $product->setPrice($priceWholesale);
        $product->setWeight($weight);
        $product->setAttributeSetId($attrSetId);
        $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);
        $saved = $this->_mageRepoProd->save($product);
        /* return product ID */
        $result = $saved->getId();
        return $result;
    }

    /**
     * Create simple product.
     *
     * @param int $mageId
     * @param string $sku
     * @param string $name
     * @param double $priceWholesale
     * @param double $weight
     */
    public function update($mageId, $sku, $name, $priceWholesale, $weight)
    {
        $product = $this->_mageRepoProd->getById($mageId);
        // SKU should not be changed
        // $product->setSku($sku);
        $product->setName($name);
        $product->setPrice($priceWholesale);
        $product->setWeight($weight);
        $this->_mageRepoProd->save($product);
    }
}