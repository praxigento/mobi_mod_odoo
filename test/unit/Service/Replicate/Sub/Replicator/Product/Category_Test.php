<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class Category_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{

    /** @var  \Mockery\MockInterface */
    private $mMageRepoCat;
    /** @var  \Mockery\MockInterface */
    private $mMageRepoCatLink;
    /** @var  \Mockery\MockInterface */
    private $mMageRepoProd;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mRepoRegistry;
    /** @var  Category */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mMageRepoProd = $this->_mock(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->mMageRepoCat = $this->_mock(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
        $this->mMageRepoCatLink = $this->_mock(\Magento\Catalog\Api\CategoryLinkRepositoryInterface::class);
        $this->mRepoRegistry = $this->_mock(\Praxigento\Odoo\Repo\IRegistry::class);
        /** create object to test */
        $this->obj = new Category(
            $this->mManObj,
            $this->mMageRepoProd,
            $this->mMageRepoCat,
            $this->mMageRepoCatLink,
            $this->mRepoRegistry
        );
    }

    public function test_checkCategoriesExistence()
    {
        /** === Test Data === */
        $ODOO_1 = 21;
        $ODOO_2 = 22;
        $MAGE_1 = 31;
        $MAGE_2 = 32;
        $IDS = [$ODOO_1, $ODOO_2];
        /** === Setup Mocks === */
        $this->obj = \Mockery::mock(Category::class . '[createMageCategory]', [
            $this->mManObj,
            $this->mMageRepoProd,
            $this->mMageRepoCat,
            $this->mMageRepoCatLink,
            $this->mRepoRegistry
        ]);
        // $mageId = $this->_repoRegistry->getCategoryMageIdByOdooId($odooId);
        $this->mRepoRegistry
            ->shouldReceive('getCategoryMageIdByOdooId')->once()
            ->with($ODOO_1)
            ->andReturn($MAGE_1);
        $this->mRepoRegistry
            ->shouldReceive('getCategoryMageIdByOdooId')->once()
            ->with($ODOO_2)
            ->andReturn(null);
        // $mageId = $this->createMageCategory('Cat #' . $odooId);
        $this->obj
            ->shouldReceive('createMageCategory')->once()
            ->andReturn($MAGE_2);
        // $this->_repoRegistry->registerCategory($mageId, $odooId);
        $this->mRepoRegistry
            ->shouldReceive('registerCategory')->once()
            ->with($MAGE_2, $ODOO_2);
        /** === Call and asserts  === */
        $this->obj->checkCategoriesExistence($IDS);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Category::class, $this->obj);
    }

    public function test_createMageCategory()
    {
        /** === Test Data === */
        $NAME = 'name';
        /** === Setup Mocks === */
        // $category = $this->_manObj->create(CategoryInterface::class);
        $mCategory = $this->_mock(\Magento\Catalog\Api\Data\CategoryInterface::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mCategory);
        // $category->setName($name);
        // $category->setIsActive(false);
        $mCategory->shouldReceive('setName', 'setIsActive');
        // $saved = $this->_mageRepoCategory->save($category);
        $this->mMageRepoCat
            ->shouldReceive('save')->once()
            ->andReturn($mCategory);
        // $result = $saved->getId();
        $mCategory->shouldReceive('getId')->once();
        /** === Call and asserts  === */
        $this->obj->createMageCategory($NAME);
    }

    public function test_replicateCategories()
    {
        /** === Test Data === */
        $PROD_ID = 32;
        $PROD_SKU = 'sku';
        $CAT_O1 = 41;
        $CAT_O3 = 43;
        $CAT_M1 = 31;
        $CAT_M2 = 32;
        $CAT_M3 = 33;
        $CAT_EXIST = [$CAT_M1, $CAT_M2];
        $CATS = [$CAT_O1, $CAT_O3];
        /** === Setup Mocks === */
        // $prod = $this->_mageRepoProd->getById($prodId);
        $mProd = $this->_mock(\Magento\Catalog\Api\Data\ProductInterface::class);
        $this->mMageRepoProd
            ->shouldReceive('getById')->once()
            ->andReturn($mProd);
        // $sku = $prod->getSku();
        $mProd->shouldReceive('getSku')->once()
            ->andReturn($PROD_SKU);
        // $catsExist = $prod->getCategoryIds();
        $mProd->shouldReceive('getCategoryIds')->once()
            ->andReturn($CAT_EXIST);
        // $catMageId = $this->_repoRegistry->getCategoryMageIdByOdooId($catOdooId);
        $this->mRepoRegistry
            ->shouldReceive('getCategoryMageIdByOdooId')->once()
            ->with($CAT_O1)
            ->andReturn($CAT_M1);
        $this->mRepoRegistry
            ->shouldReceive('getCategoryMageIdByOdooId')->once()
            ->with($CAT_O3)
            ->andReturn($CAT_M3);
        // $prodLink = $this->_manObj->create(CategoryProductLinkInterface::class);
        $mProdLink = $this->_mock(\Magento\Catalog\Api\Data\CategoryProductLinkInterface::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mProdLink);
        $mProdLink->shouldReceive('setCategoryId', 'setSku', 'setPosition');
        // $this->_mageRepoCatLink->save($prodLink);
        $this->mMageRepoCatLink
            ->shouldReceive('save')->once();
        // $this->_mageRepoCatLink->deleteByIds($catMageId, $sku);
        $this->mMageRepoCatLink
            ->shouldReceive('deleteByIds')->once()
            ->with($CAT_M2, $PROD_SKU);
        /** === Call and asserts  === */
        $this->obj->replicateCategories($PROD_ID, $CATS);
    }
}