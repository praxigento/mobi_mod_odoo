<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Replicator_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mRepoAggLot;
    /** @var  \Mockery\MockInterface */
    private $mRepoAggWrhs;
    /** @var  \Mockery\MockInterface */
    private $mRepoPv;
    /** @var  \Mockery\MockInterface */
    private $mRepoRegistry;
    /** @var  \Mockery\MockInterface */
    private $mSubProdCategory;
    /** @var  \Mockery\MockInterface */
    private $mSubProdWarehouse;
    /** @var  \Mockery\MockInterface */
    private $mSubProduct;
    /** @var  Replicator */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mRepoRegistry = $this->_mock(\Praxigento\Odoo\Repo\IRegistry::class);
        $this->mRepoAggLot = $this->_mock(\Praxigento\Odoo\Repo\Agg\ILot::class);
        $this->mRepoPv = $this->_mock(\Praxigento\Odoo\Repo\IPv::class);
        $this->mRepoAggWrhs = $this->_mock(\Praxigento\Odoo\Repo\Agg\IWarehouse::class);
        $this->mSubProduct = $this->_mock(Replicator\Product::class);
        $this->mSubProdCategory = $this->_mock(Replicator\Product\Category::class);
        $this->mSubProdWarehouse = $this->_mock(Replicator\Product\Warehouse::class);
        /** create object to test */
        $this->obj = new Replicator(
            $this->mManObj,
            $this->mRepoRegistry,
            $this->mRepoAggLot,
            $this->mRepoPv,
            $this->mRepoAggWrhs,
            $this->mSubProduct,
            $this->mSubProdCategory,
            $this->mSubProdWarehouse
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(Replicator::class, $this->obj);
    }

    public function test_processLots()
    {
        /** === Test Data === */
        $LOT = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory\ILot::class);
        $LOT->shouldReceive('getId', 'getCode', 'getExpirationDate');
        $LOTS = [$LOT];
        /** === Setup Mocks === */
        // $data = $this->_manObj->create(AggLot::class);
        $mData = $this->_mock(\Praxigento\Odoo\Data\Agg\Lot::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mData);
        $mData->shouldReceive('setOdooId', 'setCode', 'setExpDate', 'getOdooId');
        // $lotExists = $this->_repoAggLot->getByOdooId($data->getOdooId());
        $this->mRepoAggLot
            ->shouldReceive('getByOdooId')->once()
            ->andReturn(false);
        // $this->_repoAggLot->create($data);
        $this->mRepoAggLot
            ->shouldReceive('create')->once();
        /** === Call and asserts  === */
        $this->obj->processLots($LOTS);
    }

    public function test_processProductItem_active()
    {
        /** === Test Data === */
        $PROD = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory\IProduct::class);
        $PROD->shouldReceive('getId', 'getSku', 'getName', 'getPrice', 'getWeight', 'getPv');
        $ID_MAGE = 21;
        /** === Setup Mocks === */
        // $isActive = $product->getIsActive();
        $PROD->shouldReceive('getIsActive')->once()
            ->andReturn(true);
        // if (!$this->_repoRegistry->isProductRegisteredInMage($idOdoo)) {
        $this->mRepoRegistry
            ->shouldReceive('isProductRegisteredInMage')->once()
            ->andReturn(false);
        // $idMage = $this->_subProduct->create($sku, $name, $isActive, $priceWholesale, $pvWholesale,
        $this->mSubProduct
            ->shouldReceive('create')->once()
            ->andReturn($ID_MAGE);
        // $this->_repoRegistry->registerProduct($idMage, $idOdoo);
        $this->mRepoRegistry
            ->shouldReceive('registerProduct')->once();
        // $this->_repoPv->registerProductWholesalePv($idMage, $pvWholesale);
        $this->mRepoPv
            ->shouldReceive('registerProductWholesalePv')->once();
        // $categories = $product->getCategories();
        $PROD->shouldReceive('getCategories')->once()->andReturn([]);
        // $this->_subProdCategory->checkCategoriesExistence($categories);
        $this->mSubProdCategory
            ->shouldReceive('checkCategoriesExistence')->once();
        // $this->_subProdCategory->replicateCategories($idMage, $categories);
        $this->mSubProdCategory
            ->shouldReceive('replicateCategories')->once();
        // $warehouses = $product->getWarehouses();
        $PROD->shouldReceive('getWarehouses')->once()->andReturn([]);
        // $this->_subProdWarehouse->processWarehouses($idMage, $warehouses);
        $this->mSubProdWarehouse
            ->shouldReceive('processWarehouses')->once();
        /** === Call and asserts  === */
        $this->obj->processProductItem($PROD);
    }

    public function test_processProductItem_inactive()
    {
        /** === Test Data === */
        $PROD = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory\IProduct::class);
        $PROD->shouldReceive('getId', 'getSku', 'getName', 'getPrice', 'getWeight', 'getPv');
        /** === Setup Mocks === */
        // $isActive = $product->getIsActive();
        $PROD->shouldReceive('getIsActive')->once()
            ->andReturn(false);
        // if (!$this->_repoRegistry->isProductRegisteredInMage($idOdoo)) {
        $this->mRepoRegistry
            ->shouldReceive('isProductRegisteredInMage')->once()
            ->andReturn(false);
        /** === Call and asserts  === */
        $this->obj->processProductItem($PROD);
    }

    public function test_processProductItem_update()
    {
        /** === Test Data === */
        $PROD = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory\IProduct::class);
        $PROD->shouldReceive('getId', 'getSku', 'getName', 'getPrice', 'getWeight', 'getPv');
        $ID_MAGE = 21;
        /** === Setup Mocks === */
        // $isActive = $product->getIsActive();
        $PROD->shouldReceive('getIsActive')->once()
            ->andReturn(true);
        // if (!$this->_repoRegistry->isProductRegisteredInMage($idOdoo)) {
        $this->mRepoRegistry
            ->shouldReceive('isProductRegisteredInMage')->once()
            ->andReturn(true);
        // $idMage = $this->_repoRegistry->getProductMageIdByOdooId($idOdoo);
        $this->mRepoRegistry
            ->shouldReceive('getProductMageIdByOdooId')->once()
            ->andReturn($ID_MAGE);
        // $this->_subProduct->update($idMage, $name, $isActive, $priceWholesale, $weight);
        $this->mSubProduct
            ->shouldReceive('update')->once();
        // $this->_repoPv->updateProductWholesalePv($idMage, $pvWholesale);
        $this->mRepoPv
            ->shouldReceive('updateProductWholesalePv')->once();

        // $categories = $product->getCategories();
        $PROD->shouldReceive('getCategories')->once()->andReturn([]);
        // $this->_subProdCategory->checkCategoriesExistence($categories);
        $this->mSubProdCategory
            ->shouldReceive('checkCategoriesExistence')->once();
        // $this->_subProdCategory->replicateCategories($idMage, $categories);
        $this->mSubProdCategory
            ->shouldReceive('replicateCategories')->once();
        // $warehouses = $product->getWarehouses();
        $PROD->shouldReceive('getWarehouses')->once()->andReturn([]);
        // $this->_subProdWarehouse->processWarehouses($idMage, $warehouses);
        $this->mSubProdWarehouse
            ->shouldReceive('processWarehouses')->once();
        /** === Call and asserts  === */
        $this->obj->processProductItem($PROD);
    }

    /**
     * @expectedException \Exception
     */
    public function test_processWarehouses()
    {
        /** === Test Data === */
        $WRHS = $this->_mock(\Praxigento\Odoo\Data\Odoo\Inventory\IWarehouse::class);
        $WRHS->shouldReceive('getId', 'getCurrency', 'getCode');
        $WRHSS = [$WRHS];
        /** === Setup Mocks === */
        // $found = $this->_repoAggWrhs->getByOdooId($odooId);
        $this->mRepoAggWrhs
            ->shouldReceive('getByOdooId')->once()
            ->andReturn(false);
        // $aggData = $this->_manObj->create(AggWarehouse::class);
        $mAggData = new \Praxigento\Odoo\Data\Agg\Warehouse();
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mAggData);
        // $created = $this->_repoAggWrhs->create($aggData);
        $this->mRepoAggWrhs
            ->shouldReceive('create')->once()
            ->andReturn($mAggData);
        /** === Call and asserts  === */
        $this->obj->processWarehouses($WRHSS);
    }

}