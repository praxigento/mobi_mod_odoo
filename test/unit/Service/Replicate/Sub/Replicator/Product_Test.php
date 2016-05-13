<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Product_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mMageFactEntityType;
    /** @var  \Mockery\MockInterface */
    private $mMageFfactAttrSet;
    /** @var  \Mockery\MockInterface */
    private $mMageRepoProd;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  Product */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mMageFactEntityType = $this->_mock(\Magento\Eav\Model\Entity\TypeFactory::class);
        $this->mMageFfactAttrSet = $this->_mock(\Magento\Eav\Model\Entity\Attribute\SetFactory::class);
        $this->mMageRepoProd = $this->_mock(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        /** create object to test */
        $this->obj = new Product(
            $this->mManObj,
            $this->mMageFactEntityType,
            $this->mMageFfactAttrSet,
            $this->mMageRepoProd
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Product::class, $this->obj);
    }

    public function test_create()
    {
        /** === Test Data === */
        $SKU = 'sku';
        $NAME = 'product name';
        $IS_ACTIVE = true;
        $PRICE = 43.21;
        $WEIGHT = 54.3210;
        $TYPE_ID = 89;
        $ATTR_SET_ID = 8;
        $PRODUCT_ID = 543;
        /** === Setup Mocks === */
        // $entityType = $this->_mageFactEntityType->create();
        $mEntityType = $this->_mock(\Magento\Eav\Model\Entity\Type::class);
        $this->mMageFactEntityType
            ->shouldReceive('create')->once()
            ->andReturn($mEntityType);
        // $entityType->loadByCode(ProductModel::ENTITY);
        $mEntityType->shouldReceive('loadByCode')->once();
        // $entityTypeId = $entityType->getId();
        $mEntityType->shouldReceive('getId')->once()
            ->andReturn($TYPE_ID);
        // $attrSet = $this->_mageFactAttrSet->create();
        $mAttrSet = $this->_mock(\Magento\Eav\Model\Entity\Attribute\Set::class);
        $this->mMageFfactAttrSet
            ->shouldReceive('create')->once()
            ->andReturn($mAttrSet);
        // $attrSet->load($entityTypeId, AttributeSet::KEY_ENTITY_TYPE_ID);
        $mAttrSet->shouldReceive('load')->once();
        // $attrSetId = $attrSet->getId();
        $mAttrSet->shouldReceive('getId')->once()
            ->andReturn($ATTR_SET_ID);
        // $product = $this->_manObj->create(ProductInterface::class);
        $mProduct = $this->_mock(\Magento\Catalog\Api\Data\ProductInterface::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mProduct);
        $mProduct->shouldReceive('setSku', 'setName', 'setStatus', 'setPrice', 'setWeight', 'setAttributeSetId',
            'setTypeId');
        // $saved = $this->_mageRepoProd->save($product);
        $this->mMageRepoProd
            ->shouldReceive('save')->once()
            ->andReturn($mProduct);
        // $result = $saved->getId();
        $mProduct->shouldReceive('getId')->once()
            ->andReturn($PRODUCT_ID);
        /** === Call and asserts  === */
        $res = $this->obj->create($SKU, $NAME, $IS_ACTIVE, $PRICE, $WEIGHT);
        $this->assertEquals($PRODUCT_ID, $res);
    }

    public function test_update()
    {
        /** === Test Data === */
        $MAGE_ID = 1234;
        $NAME = 'product name';
        $IS_ACTIVE = true;
        $PRICE = 43.21;
        $WEIGHT = 54.3210;
        /** === Setup Mocks === */
        // $product = $this->_mageRepoProd->getById($mageId);
        $mProduct = $this->_mock(\Magento\Catalog\Api\Data\ProductInterface::class);
        $this->mMageRepoProd
            ->shouldReceive('getById')->once()
            ->andReturn($mProduct);
        $mProduct->shouldReceive('setName', 'setStatus', 'setPrice', 'setWeight');
        // $this->_mageRepoProd->save($product);
        $this->mMageRepoProd
            ->shouldReceive('save')->once();
        /** === Call and asserts  === */
        $this->obj->update($MAGE_ID, $NAME, $IS_ACTIVE, $PRICE, $WEIGHT);
    }

}