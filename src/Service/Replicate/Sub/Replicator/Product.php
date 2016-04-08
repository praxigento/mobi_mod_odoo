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
    protected $_factAttrSet;
    /** @var EntityTypeFactory */
    protected $_factEntityType;
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var IProductRepo */
    protected $_repoProd;

    public function __construct(
        ObjectManagerInterface $manObj,
        EntityTypeFactory $factEntityType,
        AttributeSetFactory $factAttrSet,
        IProductRepo $repoProd
    ) {
        $this->_manObj = $manObj;
        $this->_factEntityType = $factEntityType;
        $this->_factAttrSet = $factAttrSet;
        $this->_repoProd = $repoProd;
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
        $entityType = $this->_factEntityType
            ->create()
            ->loadByCode(\Magento\Catalog\Model\Product::ENTITY);
        $entityTypeId = $entityType->getId();
        $attrSet = $this->_factAttrSet
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
        $saved = $this->_repoProd->save($product);
        /* return product ID */
        $result = $saved->getId();
        return $result;
    }
}