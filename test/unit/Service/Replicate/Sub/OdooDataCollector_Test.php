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

    public function test__getLinesTotals()
    {
        /** === Test Data === */
        $TOTAl = 100;
        $DISCOUNT = 10;
        $TAX = 2.31;
        $LINE = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line::class);
        $LINES = [$LINE];
        /** === Setup Mocks === */
        // $amount += $line->getPriceTotalLine();
        $LINE->shouldReceive('getPriceTotalLine')->once()->andReturn($TOTAl);
        // $discount += $line->getPriceDiscountLine();
        $LINE->shouldReceive('getPriceDiscountLine')->once()->andReturn($DISCOUNT);
        // $tax += $line->getPriceTaxLine();
        $LINE->shouldReceive('getPriceTaxLine')->once()->andReturn($TAX);
        /** === Call and asserts  === */
        $res = $this->obj->_getLinesTotals($LINES);
        $this->assertTrue(is_array($res));
        $this->assertEquals($TOTAl, $res[OdooDataCollector::AMOUNT]);
        $this->assertEquals($DISCOUNT, $res[OdooDataCollector::DISCOUNT]);
        $this->assertEquals($TAX, $res[OdooDataCollector::TAX]);
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(OdooDataCollector::class, $this->obj);
    }

    public function test_getAddressBilling()
    {
        /** === Test Data === */
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $RESULT = $this->_mock(\Praxigento\Odoo\Data\Odoo\Contact::class);
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(OdooDataCollector::class . '[_extractContact]', $this->objArgs);
        /** === Setup Mocks === */
        // $addrMage = $mageOrder->getBillingAddress();
        $mAddrMage = $this->_mock(\Magento\Sales\Api\Data\OrderAddressInterface::class);
        $ORDER->shouldReceive('getBillingAddress')->once()
            ->andReturn($mAddrMage);
        // $result = $this->_extractContact($addrMage);
        $this->obj->shouldReceive('_extractContact')->once()
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getAddressBilling($ORDER);
        $this->assertEquals($RESULT, $res);
    }

    public function test_getAddressShipping()
    {
        /** === Test Data === */
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        $RESULT = $this->_mock(\Praxigento\Odoo\Data\Odoo\Contact::class);
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(OdooDataCollector::class . '[_extractContact]', $this->objArgs);
        /** === Setup Mocks === */
        // $addrMage = $mageOrder->getShippingAddress();
        $mAddrMage = $this->_mock(\Magento\Sales\Api\Data\OrderAddressInterface::class);
        $ORDER->shouldReceive('getShippingAddress')->once()
            ->andReturn($mAddrMage);
        // $result = $this->_extractContact($addrMage);
        $this->obj->shouldReceive('_extractContact')->once()
            ->andReturn($RESULT);
        /** === Call and asserts  === */
        $res = $this->obj->getAddressShipping($ORDER);
        $this->assertEquals($RESULT, $res);
    }

    public function test_getSaleOrder()
    {
        /** === Test Data === */
        $ID = 4;
        $WRHS_ID_ODOO = 16;
        $INCREMENTAL_ID = '100000032';
        $CUR = 'USD';
        $PV_TOTAL = 21.34;
        $DATE_PAID = '2016-08-21';
        $AMOUNT_TOTAL = 100.44;
        $AMOUNT_DISCOUNT = 10.21;
        $AMOUNT_TAX = 14.28;
        $AMOUNT_SHIPPING_TOTAL = 10.50;
        $AMOUNT_SHIPPING_DISCOUNT = 1.24;
        $AMOUNT_SHIPPING_TAX = 2.50;
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(
            OdooDataCollector::class . '[_extractWarehouseIdOdoo, getSaleOrderCustomer, getAddressBilling, getAddressShipping, getSaleOrderLines, getSaleOrderShipping, getSaleOrderPayments, _getLinesTotals]',
            $this->objArgs
        );
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder::class);
        $mResult = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mResult);
        // $orderIdMage = (int)$mageOrder->getId();
        $ORDER->shouldReceive('getId')->once()->andReturn($ID);
        // $warehouseIdOdoo = (int)$this->_extractWarehouseIdOdoo($mageOrder);
        $this->obj->shouldReceive('_extractWarehouseIdOdoo')->once()
            ->andReturn($WRHS_ID_ODOO);
        // $number = $mageOrder->getIncrementId();
        $ORDER->shouldReceive('getIncrementId')->once()->andReturn($INCREMENTAL_ID);
        // $customer = $this->getSaleOrderCustomer($mageOrder);
        $mCustomer = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Customer::class);
        $this->obj->shouldReceive('getSaleOrderCustomer')->once()
            ->andReturn($mCustomer);
        // $addrBilling = $this->getAddressBilling($mageOrder);
        $mAddrBilling = $this->_mock(\Praxigento\Odoo\Data\Odoo\Contact::class);
        $this->obj->shouldReceive('getAddressBilling')->once()
            ->andReturn($mAddrBilling);
        // $addrShipping = $this->getAddressShipping($mageOrder);
        $mAddrShipping = $this->_mock(\Praxigento\Odoo\Data\Odoo\Contact::class);
        $this->obj->shouldReceive('getAddressShipping')->once()
            ->andReturn($mAddrShipping);
        // $priceCurrency = $mageOrder->getBaseCurrencyCode();
        $ORDER->shouldReceive('getBaseCurrencyCode')->once()->andReturn($CUR);
        // $pvOrder = $this->_repoPvSale->getById($orderIdMage);
        $mPvOrder = $this->_mock(\Praxigento\Pv\Data\Entity\Sale::class);
        $this->mRepoPvSale
            ->shouldReceive('getById')->once()
            ->andReturn($mPvOrder);
        // $pvTotal = $this->_manFormat->toNumber($pvOrder->getTotal());
        $mPvOrder->shouldReceive('getTotal')->once()
            ->andReturn($PV_TOTAL);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->with($PV_TOTAL)
            ->andReturn($PV_TOTAL);
        // $datePaid = $pvOrder->getDatePaid();
        $mPvOrder->shouldReceive('getDatePaid')->once()
            ->andReturn($DATE_PAID);
        // $lines = $this->getSaleOrderLines($mageOrder);
        $mLines = ['lines'];
        $this->obj->shouldReceive('getSaleOrderLines')->once()
            ->andReturn($mLines);
        // $shipping = $this->getSaleOrderShipping($mageOrder);
        $mShipping = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Shipping::class);
        $this->obj->shouldReceive('getSaleOrderShipping')->once()
            ->andReturn($mShipping);
        // $payments = $this->getSaleOrderPayments($mageOrder);
        $mPayments = ['payments'];
        $this->obj->shouldReceive('getSaleOrderPayments')->once()
            ->andReturn($mPayments);
        // $totals = $this->_getLinesTotals($lines);
        $mTotals = [
            OdooDataCollector::AMOUNT => $AMOUNT_TOTAL,
            OdooDataCollector::DISCOUNT => $AMOUNT_DISCOUNT,
            OdooDataCollector::TAX => $AMOUNT_TAX
        ];
        $this->obj->shouldReceive('_getLinesTotals')->once()
            ->andReturn($mTotals);
        // $priceTotal = $totals[self::AMOUNT] + $shipping->getPriceAmountTotal();
        $mShipping->shouldReceive('getPriceAmountTotal')->once()
            ->andReturn($AMOUNT_SHIPPING_TOTAL);
        // $priceTotal = $this->_manFormat->toNumber($priceTotal);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->andReturn($AMOUNT_TOTAL + $AMOUNT_SHIPPING_TOTAL);
        // $priceTax = $totals[self::TAX] + $shipping->getPriceTaxAmount();
        $mShipping->shouldReceive('getPriceTaxAmount')->once()
            ->andReturn($AMOUNT_SHIPPING_TAX);
        // $priceTax = $this->_manFormat->toNumber($priceTax);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->andReturn($AMOUNT_TAX + $AMOUNT_SHIPPING_TAX);
        // $priceDiscount = $totals[self::DISCOUNT] + $shipping->getPriceDiscount();
        $mShipping->shouldReceive('getPriceDiscount')->once()
            ->andReturn($AMOUNT_SHIPPING_DISCOUNT);
        // $priceDiscount = $this->_manFormat->toNumber($priceDiscount);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->andReturn($AMOUNT_DISCOUNT + $AMOUNT_SHIPPING_DISCOUNT);
        //
        $mResult->shouldReceive('setIdMage')->once();
        $mResult->shouldReceive('setWarehouseIdOdoo')->once();
        $mResult->shouldReceive('setNumber')->once();
        $mResult->shouldReceive('setDatePaid')->once();
        $mResult->shouldReceive('setCustomer')->once();
        $mResult->shouldReceive('setAddrBilling')->once();
        $mResult->shouldReceive('setAddrShipping')->once();
        $mResult->shouldReceive('setPriceCurrency')->once();
        $mResult->shouldReceive('setPriceTotal')->once();
        $mResult->shouldReceive('setPriceTax')->once();
        $mResult->shouldReceive('setPriceDiscount')->once();
        $mResult->shouldReceive('setPvTotal')->once();
        $mResult->shouldReceive('setLines')->once();
        $mResult->shouldReceive('setShipping')->once();
        $mResult->shouldReceive('setPayments')->once();
        /** === Call and asserts  === */
        $res = $this->obj->getSaleOrder($ORDER);
        $this->assertEquals($mResult, $res);
    }

    public function test_getSaleOrderCustomer()
    {
        /** === Test Data === */
        $CUSTOMER_ID = 32;
        $MLM_ID = '123123123';
        $NAME = 'John Dow';
        $GROUP = 'distributor';
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder\Customer::class);
        $mResult = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Customer::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mResult);
        // $custMageId = (int)$mageOrder->getCustomerId();
        $ORDER->shouldReceive('getCustomerId')->once()->andReturn($CUSTOMER_ID);
        // $dwnlCust = $this->_repoDwnlCustomer->getById($custMageId);
        $mDwnlCust = $this->_mock(\Praxigento\Downline\Data\Entity\Customer::class);
        $this->mRepoDwnlCustomer
            ->shouldReceive('getById')->once()
            ->with($CUSTOMER_ID)
            ->andReturn($mDwnlCust);
        // $ref = $dwnlCust->getHumanRef();
        $mDwnlCust->shouldReceive('getHumanRef')->once()->andReturn($MLM_ID);
        // $name = $mageOrder->getCustomerName();
        $ORDER->shouldReceive('getCustomerName')->once()->andReturn($NAME);
        // $mageCust = $this->_repoMageCustomer->getById($custMageId);
        $mMageCust = $this->_mock(\Magento\Customer\Api\Data\CustomerInterface::class);
        $this->mRepoMageCustomer
            ->shouldReceive('getById')->once()
            ->andReturn($mMageCust);
        // $groupCode = $this->_manBusinessCodes->getBusCodeForCustomerGroup($mageCust);
        $this->mManBusinessCodes
            ->shouldReceive('getBusCodeForCustomerGroup')->once()
            ->andReturn($GROUP);
        // init Odoo data object
        $mResult->shouldReceive('setIdMage')->with($CUSTOMER_ID)->once();
        $mResult->shouldReceive('setIdMlm')->with($MLM_ID)->once();
        $mResult->shouldReceive('setName')->with($NAME)->once();
        $mResult->shouldReceive('setGroupCode')->with($GROUP)->once();
        /** === Call and asserts  === */
        $res = $this->obj->getSaleOrderCustomer($ORDER);
        $this->assertEquals($mResult, $res);
    }

    public function test_getSaleOrderLines()
    {
        /** === Test Data === */
        $ORDER_ID = 4;
        $STORE_ID = 2;
        $STOCK_ID = 8;
        $PROD_ID_ODOO = 64;
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Mock object itself === */
        $this->obj = \Mockery::mock(OdooDataCollector::class . '[_extractLine, _extractLineLot]', $this->objArgs);
        /** === Setup Mocks === */
        // $orderId = $mageOrder->getId();
        $ORDER->shouldReceive('getId')->once()->andReturn($ORDER_ID);
        // $storeId = $mageOrder->getStoreId();
        $ORDER->shouldReceive('getStoreId')->once()->andReturn($STORE_ID);
        // $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $this->mManStock
            ->shouldReceive('getStockIdByStoreId')->once()
            ->andReturn($STOCK_ID);
        // $aggSaleOrderItems = $this->_repoAggSaleOrderItem->getByOrderAndStock($orderId, $stockId);
        $mItem1 = $this->_mock(\Praxigento\Odoo\Data\Agg\SaleOrderItem::class);
        $mItem2 = $this->_mock(\Praxigento\Odoo\Data\Agg\SaleOrderItem::class);
        $mAggSaleOrderItems = [$mItem1, $mItem2];
        $this->mRepoAggSaleOrderItem
            ->shouldReceive('getByOrderAndStock')->once()
            ->andReturn($mAggSaleOrderItems);
        // First Iteration
        // $productIdOdoo = $item->getOdooIdProduct();
        $mItem1->shouldReceive('getOdooIdProduct')->once()->andReturn($PROD_ID_ODOO);
        // $line = $this->_extractLine($item);
        $mLine = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line::class);
        $this->obj->shouldReceive('_extractLine')->once()->andReturn($mLine);
        // $lots = $line->getLots();
        $mLot1 = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot::class);
        $mLine->shouldReceive('getLots')->once()->andReturn([$mLot1]);
        // $lot = $this->_extractLineLot($item);
        $mLine->shouldReceive('getLots')->once()->andReturn([]); // empty array on first iteration
        // $lot = $this->_extractLineLot($item);
        $mLot1 = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot::class);
        $this->obj->shouldReceive('_extractLineLot')->once()
            ->andReturn($mLot1);
        // $line->setLots($lots);
        $mLine->shouldReceive('setLots')->with([$mLot1])->once();
        //
        // Second Iteration
        // $productIdOdoo = $item->getOdooIdProduct();
        $mItem2->shouldReceive('getOdooIdProduct')->once()->andReturn($PROD_ID_ODOO);
        // $lots = $line->getLots();
        $mLine->shouldReceive('getLots')->once()->andReturn([$mLot1]); // return array with first lot only
        // $lot = $this->_extractLineLot($item);
        $mLot2 = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot::class);
        $this->obj->shouldReceive('_extractLineLot')->once()
            ->andReturn($mLot2);
        // $line->setLots($lots);
        $mLine->shouldReceive('setLots')->with([$mLot1, $mLot2])->once();
        /** === Call and asserts  === */
        $res = $this->obj->getSaleOrderLines($ORDER);
        $this->assertTrue(is_array($res));
        $this->assertTrue(count($res) > 0);
    }


    public function test_getSaleOrderPayments()
    {
        /** === Test Data === */
        $PAYMENT_CODE = 'cash';
        $AMOUNT_ORDERED = 100.25;
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Setup Mocks === */
        // $odooPayment = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\Payment::class);
        $mOdooPayment = $this->_mock(\Praxigento\Odoo\Data\Odoo\Payment::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mOdooPayment);
        // $magePayment = $mageOrder->getPayment();
        $mMagePayment = $this->_mock(\Magento\Sales\Api\Data\OrderPaymentInterface::class);
        $ORDER->shouldReceive('getPayment')->once()->andReturn($mMagePayment);
        // $code = $this->_manBusinessCodes->getBusCodeForPaymentMethod($magePayment);
        $this->mManBusinessCodes
            ->shouldReceive('getBusCodeForPaymentMethod')->once()
            ->andReturn($PAYMENT_CODE);
        // $ordered = $magePayment->getBaseAmountOrdered();
        $mMagePayment->shouldReceive('getBaseAmountOrdered')->once()
            ->andReturn($AMOUNT_ORDERED);
        // $amount = $this->_manFormat->toNumber($ordered);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->with($AMOUNT_ORDERED)
            ->andReturn($AMOUNT_ORDERED);
        //
        $mOdooPayment->shouldReceive('setCode')->once();
        $mOdooPayment->shouldReceive('setAmount')->once();
        /** === Call and asserts  === */
        $res = $this->obj->getSaleOrderPayments($ORDER);
        $this->assertTrue(is_array($res));
        $this->assertTrue(count($res) > 0);
    }


    public function test_getSaleOrderShipping()
    {
        /** === Test Data === */
        $SHIPPING_CODE = 'flat';
        $SHIPPING_AMOUNT = 100.20;
        $SHIPPING_DISCOUNT = 10.22;
        $SHIPPING_TAX = 5.25;
        $SHIPPING_TAX_PERCENT = 0.21;
        $TOTAL = 100000.00;
        $ORDER = $this->_mock(\Magento\Sales\Api\Data\OrderInterface::class);
        /** === Setup Mocks === */
        // $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder\Shipping::class);
        $mResult = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Shipping::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mResult);
        // $code = $this->_manBusinessCodes->getBusCodeForShippingMethod($mageOrder);
        $this->mManBusinessCodes
            ->shouldReceive('getBusCodeForShippingMethod')->once()
            ->andReturn($SHIPPING_CODE);
        // $priceAmount = $mageOrder->getBaseShippingAmount();
        $ORDER->shouldReceive('getBaseShippingAmount')->once()->andReturn($SHIPPING_AMOUNT);
        // $priceAmount = $this->_manFormat->toNumber($priceAmount);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->andReturn($SHIPPING_AMOUNT);
        // $priceDiscount = $mageOrder->getBaseShippingDiscountAmount();
        $ORDER->shouldReceive('getBaseShippingDiscountAmount')->once()->andReturn($SHIPPING_DISCOUNT);
        // $priceDiscount = $this->_manFormat->toNumber($priceDiscount);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->andReturn($SHIPPING_DISCOUNT);
        // $priceTaxAmount = $mageOrder->getBaseShippingTaxAmount();
        $ORDER->shouldReceive('getBaseShippingTaxAmount')->once()->andReturn($SHIPPING_TAX);
        // $priceTaxAmount = $this->_manFormat->toNumber($priceTaxAmount);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->andReturn($SHIPPING_TAX);
        // $priceTaxPercent = $this->_manFormat->toNumber($priceTaxPercent, Cfg::ODOO_API_PERCENT_ROUND);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->andReturn($SHIPPING_TAX_PERCENT);
        // $priceAmountTotal = $this->_manFormat->toNumber($priceAmountTotal);
        $this->mManFormat
            ->shouldReceive('toNumber')->once()
            ->andReturn($TOTAL);
        // setters
        $mResult->shouldReceive('setCode')->once();
        $mResult->shouldReceive('setPriceAmount')->once();
        $mResult->shouldReceive('setPriceDiscount')->once();
        $mResult->shouldReceive('setPriceTaxPercent')->once();
        $mResult->shouldReceive('setPriceTaxAmount')->once();
        $mResult->shouldReceive('setPriceAmountTotal')->once();
        /** === Call and asserts  === */
        $res = $this->obj->getSaleOrderShipping($ORDER);
    }
}