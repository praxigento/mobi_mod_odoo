<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class OdooDataCollector_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mManBusinessCodes;
    /** @var  \Mockery\MockInterface */
    private $mManFormat;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  \Mockery\MockInterface */
    private $mManStock;
    /** @var  \Mockery\MockInterface */
    private $mRepoAggSaleOrderItem;
    /** @var  \Mockery\MockInterface */
    private $mRepoDwnlCustomer;
    /** @var  \Mockery\MockInterface */
    private $mRepoMageCustomer;
    /** @var  \Mockery\MockInterface */
    private $mRepoPvSale;
    /** @var  \Mockery\MockInterface */
    private $mRepoPvSaleItem;
    /** @var  \Mockery\MockInterface */
    private $mRepoWarehouse;
    /** @var  \Mockery\MockInterface */
    private $mRepoWrhsQtySale;
    /** @var  OdooDataCollector */
    private $obj;
    /** @var array Constructor arguments for object mocking */
    private $objArgs = [];

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mManObj = $this->_mockObjectManager();
        $this->mRepoMageCustomer = $this->_mock(\Magento\Customer\Api\CustomerRepositoryInterface::class);
        $this->mManStock = $this->_mock(\Praxigento\Warehouse\Tool\IStockManager::class);
        $this->mManBusinessCodes = $this->_mock(\Praxigento\Odoo\Tool\IBusinessCodesManager::class);
        $this->mManFormat = $this->_mock(\Praxigento\Core\Tool\IFormat::class);
        $this->mRepoDwnlCustomer = $this->_mock(\Praxigento\Downline\Repo\Entity\ICustomer::class);
        $this->mRepoPvSale = $this->_mock(\Praxigento\Pv\Repo\Entity\ISale::class);
        $this->mRepoPvSaleItem = $this->_mock(\Praxigento\Pv\Repo\Entity\Sale\IItem::class);
        $this->mRepoWrhsQtySale = $this->_mock(\Praxigento\Warehouse\Repo\Entity\Quantity\ISale::class);
        $this->mRepoAggSaleOrderItem = $this->_mock(\Praxigento\Odoo\Repo\Agg\ISaleOrderItem::class);
        $this->mRepoWarehouse = $this->_mock(\Praxigento\Odoo\Repo\Entity\IWarehouse::class);
        /** reset args. to create mock of the tested object */
        $this->objArgs = [
            $this->mManObj,
            $this->mRepoMageCustomer,
            $this->mManStock,
            $this->mManBusinessCodes,
            $this->mManFormat,
            $this->mRepoDwnlCustomer,
            $this->mRepoPvSale,
            $this->mRepoPvSaleItem,
            $this->mRepoWrhsQtySale,
            $this->mRepoAggSaleOrderItem,
            $this->mRepoWarehouse
        ];
        /** create object to test */
        $this->obj = new OdooDataCollector(
            $this->mManObj,
            $this->mRepoMageCustomer,
            $this->mManStock,
            $this->mManBusinessCodes,
            $this->mManFormat,
            $this->mRepoDwnlCustomer,
            $this->mRepoPvSale,
            $this->mRepoPvSaleItem,
            $this->mRepoWrhsQtySale,
            $this->mRepoAggSaleOrderItem,
            $this->mRepoWarehouse
        );
    }

    public function test__extractContact()
    {
        /** === Test Data === */
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $ADDR_MAGE */
        $ADDR_MAGE = $this->_mock(\Magento\Sales\Api\Data\OrderAddressInterface::class);
        $ADDR_MAGE->shouldReceive('getName')->once()->andReturn('Name');
        $ADDR_MAGE->shouldReceive('getTelephone')->once()->andReturn('Telephone');
        $ADDR_MAGE->shouldReceive('getEmail')->once()->andReturn('Email');
        $ADDR_MAGE->shouldReceive('getCountryId')->once()->andReturn('CountryId');
        $ADDR_MAGE->shouldReceive('getRegionCode')->once()->andReturn('RegionCode');
        $ADDR_MAGE->shouldReceive('getCity')->once()->andReturn('City');
        $ADDR_MAGE->shouldReceive('getStreet')->once()->andReturn(['Street']);
        $ADDR_MAGE->shouldReceive('getPostcode')->once()->andReturn('Postcode');
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\Contact::class);
        $mResult = $this->_mock(\Praxigento\Odoo\Data\Odoo\Contact::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mResult);
        //
        $mResult->shouldReceive('setName')->once();
        $mResult->shouldReceive('setPhone')->once();
        $mResult->shouldReceive('setEmail')->once();
        $mResult->shouldReceive('setCountry')->once();
        $mResult->shouldReceive('setState')->once();
        $mResult->shouldReceive('setCity')->once();
        $mResult->shouldReceive('setStreet')->once();
        $mResult->shouldReceive('setZip')->once();
        /** === Call and asserts  === */
        $res = $this->obj->_extractContact($ADDR_MAGE);
        $this->assertTrue($res instanceof \Praxigento\Odoo\Data\Odoo\Contact);
    }

    public function test__extractLine()
    {
        /** === Test Data === */
        /** @var \Praxigento\Odoo\Data\Agg\SaleOrderItem $ITEM */
        $ITEM = $this->_mock(\Praxigento\Odoo\Data\Agg\SaleOrderItem::class);
        $ITEM->shouldReceive('getOdooIdProduct')->once()->andReturn('21');
        $ITEM->shouldReceive('getItemQty')->once()->andReturn('ItemQty');
        $ITEM->shouldReceive('getPriceUnitOrig')->once()->andReturn('PriceUnitOrig');
        $ITEM->shouldReceive('getPriceDiscount')->once()->andReturn('riceDiscount');
        $ITEM->shouldReceive('getPriceTaxPercent')->once()->andReturn('PriceTaxPercent');
        $ITEM->shouldReceive('getPvUnit')->once()->andReturn('PvUnit');
        $ITEM->shouldReceive('getPvDiscount')->once()->andReturn('PvDiscount');
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line::class);
        $mResult = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mResult);
        //
        $this->mManFormat->shouldReceive('toNumber')->andReturn('formatted');
        //
        $mResult->shouldReceive('setProductIdOdoo')->once();
        $mResult->shouldReceive('setQtyLine')->once();
        $mResult->shouldReceive('setLots')->once();
        $mResult->shouldReceive('setPriceSaleUnit')->once();
        $mResult->shouldReceive('setPriceDiscountLine')->once();
        $mResult->shouldReceive('setPriceTaxPercent')->once();
        $mResult->shouldReceive('setPriceTotalLine')->once();
        $mResult->shouldReceive('setPriceTaxLine')->once();
        $mResult->shouldReceive('setPvSaleUnit')->once();
        $mResult->shouldReceive('setPvDiscountLine')->once();
        /** === Call and asserts  === */
        $res = $this->obj->_extractLine($ITEM);
        $this->assertTrue($res instanceof \Praxigento\Odoo\Data\Odoo\SaleOrder\Line);
    }

    public function test__extractLineLot()
    {
        /** === Test Data === */
        /** @var \Praxigento\Odoo\Data\Agg\SaleOrderItem $ITEM */
        $ITEM = $this->_mock(\Praxigento\Odoo\Data\Agg\SaleOrderItem::class);
        $ITEM->shouldReceive('getOdooIdLot')->once()->andReturn('OdooIdLot');
        $ITEM->shouldReceive('getLotQty')->once()->andReturn('LotQty');
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot::class);
        $mResult = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mResult);
        //
        $this->mManFormat->shouldReceive('toNumber')->andReturn('formatted');
        //
        $mResult->shouldReceive('setIdOdoo')->once();
        $mResult->shouldReceive('setQty')->once();
        /** === Call and asserts  === */
        $res = $this->obj->_extractLineLot($ITEM);
        $this->assertTrue($res instanceof \Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot);
    }

    public function test__extractWarehouseIdOdoo()
    {
        /** === Test Data === */
        $ODOO_ID = 32;
        $STORE_ID = 4;
        $STOCK_ID = 8;
        /** @var \Magento\Sales\Api\Data\OrderInterface $MAGE_ORDER */
        $MAGE_ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $MAGE_ORDER->shouldReceive('getStoreId')->once()->andReturn($STORE_ID);
        /** === Setup Mocks === */
        // $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $this->mManStock
            ->shouldReceive('getStockIdByStoreId')->once()
            ->andReturn($STOCK_ID);
        // $warehouse = $this->_repoWarehouse->getById($stockId);
        $mWarehouse = $this->_mock(\Praxigento\Odoo\Data\Entity\Warehouse::class);
        $this->mRepoWarehouse
            ->shouldReceive('getById')->once()
            ->andReturn($mWarehouse);
        // $result = $warehouse->getOdooRef();
        $mWarehouse->shouldReceive('getOdooRef')->once()
            ->andReturn($ODOO_ID);
        /** === Call and asserts  === */
        $res = $this->obj->_extractWarehouseIdOdoo($MAGE_ORDER);
        $this->assertEquals($ODOO_ID, $res);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(OdooDataCollector::class, $this->obj);
    }
}