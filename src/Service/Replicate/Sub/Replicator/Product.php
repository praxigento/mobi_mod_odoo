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
        /**
         * Retrieve entity type ID & attribute set ID.
         */
        /** @var  $entityType \Magento\Eav\Model\Entity\Type */
        $entityType = $this->_mageFactEntityType
            ->create()
            ->loadByCode(ProductModel::ENTITY);
        $entityTypeId = $entityType->getId();
        $attrSet = $this->_mageFactAttrSet
            ->create()
            ->load($entityTypeId, AttributeSet::KEY_ENTITY_TYPE_ID);
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