<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub;

use Praxigento\Odoo\Config as Cfg;

/**
 * Extract data from Magento Sales Order and collect additional data to compose Odoo Sales Order.
 */
class OdooDataCollector
{
    /**#@+
     * Labels for associative array with sum of totals by lines
     */
    const AMOUNT = 'amount';
    const DISCOUNT = 'discount';
    const TAX = 'tax';
    /**#@- */

    /** @var  \Praxigento\Odoo\Tool\IBusinessCodesManager */
    protected $_manBusinessCodes;
    /** @var  \Praxigento\Core\Tool\IFormat */
    protected $_manFormat;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;
    /** @var \Praxigento\Odoo\Repo\Agg\ISaleOrderItem */
    protected $_repoAggSaleOrderItem;
    /** @var \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $_repoDwnlCustomer;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $_repoMageCustomer;
    /** @var \Praxigento\Pv\Repo\Entity\ISale */
    protected $_repoPvSale;
    /** @var \Praxigento\Pv\Repo\Entity\Sale\IItem */
    protected $_repoPvSaleItem;
    /** @var \Praxigento\Odoo\Repo\Entity\IWarehouse */
    protected $_repoWarehouse;
    /** @var \Praxigento\Warehouse\Repo\Entity\Quantity\ISale */
    protected $_repoWrhsQtySale;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoMageCustomer,
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Odoo\Tool\IBusinessCodesManager $manBusinessCodes,
        \Praxigento\Core\Tool\IFormat $manFormat,
        \Praxigento\Downline\Repo\Entity\ICustomer $repoDwnlCustomer,
        \Praxigento\Pv\Repo\Entity\ISale $repoPvSale,
        \Praxigento\Pv\Repo\Entity\Sale\IItem $repoPvSaleItem,
        \Praxigento\Warehouse\Repo\Entity\Quantity\ISale $repoWrhsQtySale,
        \Praxigento\Odoo\Repo\Agg\ISaleOrderItem $repoAggSaleOrderItem,
        \Praxigento\Odoo\Repo\Entity\IWarehouse $repoWarehouse
    ) {
        $this->_manObj = $manObj;
        $this->_repoMageCustomer = $repoMageCustomer;
        $this->_manStock = $manStock;
        $this->_manBusinessCodes = $manBusinessCodes;
        $this->_manFormat = $manFormat;
        $this->_repoDwnlCustomer = $repoDwnlCustomer;
        $this->_repoPvSale = $repoPvSale;
        $this->_repoPvSaleItem = $repoPvSaleItem;
        $this->_repoWrhsQtySale = $repoWrhsQtySale;
        $this->_repoAggSaleOrderItem = $repoAggSaleOrderItem;
        $this->_repoWarehouse = $repoWarehouse;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $addrMage
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    public function _extractContact(\Magento\Sales\Api\Data\OrderAddressInterface $addrMage)
    {
        $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\Contact::class);
        /* collect data */
        $name = $addrMage->getName();
        $phone = $addrMage->getTelephone();
        $email = $addrMage->getEmail();
        $country = $addrMage->getCountryId();
        $state = $addrMage->getRegionCode();
        $city = $addrMage->getCity();
        $street = $addrMage->getStreet(); // street data is array
        $street = implode('', $street);
        $zip = $addrMage->getPostcode();
        /* init Odoo data object */
        if ($name) $result->setName($name);
        if ($phone) $result->setPhone($phone);
        if ($email) $result->setEmail($email);
        if ($country) $result->setCountry($country);
        if ($state) $result->setState($state);
        if ($city) $result->setCity($city);
        if ($street) $result->setStreet($street);
        if ($zip) $result->setZip($zip);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Data\Agg\SaleOrderItem $item
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line
     */
    public function _extractLine(\Praxigento\Odoo\Data\Agg\SaleOrderItem $item)
    {
        $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line::class);
        /* collect data */
        $productIdOdoo = (int)$item->getOdooIdProduct();
        $qtyLine = $this->_manFormat->toNumber($item->getItemQty());
        /* price attributes */
        $priceSaleUnit = $this->_manFormat->toNumber($item->getPriceUnitOrig());
        $priceDiscountLine = abs($this->_manFormat->toNumber($item->getPriceDiscount()));
        /* price related calculated attributes */
        $priceTaxPercent = $this->_manFormat->toNumber(
            $item->getPriceTaxPercent() / 100,
            Cfg::ODOO_API_PERCENT_ROUND
        );
        $priceTotalLine = ($qtyLine * $priceSaleUnit - $priceDiscountLine) * (1 + $priceTaxPercent);
        $priceTotalLine = $this->_manFormat->toNumber($priceTotalLine);
        $priceTaxLine = $priceTotalLine / (1 + $priceTaxPercent) * $priceTaxPercent;
        $priceTaxLine = $this->_manFormat->toNumber($priceTaxLine);
        /* PV attributes */
        $pvSaleUnit = $this->_manFormat->toNumber($item->getPvUnit());
        $pvDiscountLine = abs($this->_manFormat->toNumber($item->getPvDiscount()));
        /* init Odoo data object */
        $result->setProductIdOdoo($productIdOdoo);
        $result->setQtyLine($qtyLine);
        $result->setLots([]); // will be initialized later
        $result->setPriceSaleUnit($priceSaleUnit);
        $result->setPriceDiscountLine($priceDiscountLine);
        $result->setPriceTaxPercent($priceTaxPercent);
        $result->setPriceTotalLine($priceTotalLine);
        $result->setPriceTaxLine($priceTaxLine);
        $result->setPvSaleUnit($pvSaleUnit);
        $result->setPvDiscountLine($pvDiscountLine);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Data\Agg\SaleOrderItem $item
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot
     */
    public function _extractLineLot(\Praxigento\Odoo\Data\Agg\SaleOrderItem $item)
    {
        $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot::class);
        $idOdoo = (int)$item->getOdooIdLot();
        $qty = $this->_manFormat->toNumber($item->getLotQty());
        if ($idOdoo != \Praxigento\Odoo\Data\Agg\Lot::NULL_LOT_ID) $result->setIdOdoo($idOdoo);
        $result->setQty($qty);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return int
     */
    public function _extractWarehouseIdOdoo(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $storeId = $mageOrder->getStoreId();
        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $warehouse = $this->_repoWarehouse->getById($stockId);
        $result = $warehouse->getOdooRef();
        return $result;
    }

    /**
     * Calculates totals for all lines (amount, discount, tax).
     *
     * @param \Praxigento\Odoo\Data\Odoo\SaleOrder\Line[] $lines
     * @return array
     */
    public function _getLinesTotals($lines)
    {
        $result = [];
        $amount = 0;
        $discount = 0;
        $tax = 0;
        foreach ($lines as $line) {
            $amount += $line->getPriceTotalLine();
            $discount += $line->getPriceDiscountLine();
            $tax += $line->getPriceTaxLine();
        }
        $result[self::AMOUNT] = $amount;
        $result[self::DISCOUNT] = $discount;
        $result[self::TAX] = $tax;
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    public function getAddressBilling(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $addrMage */
        $addrMage = $mageOrder->getBillingAddress();
        $result = $this->_extractContact($addrMage);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    public function getAddressShipping(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $addrMage */
        $addrMage = $mageOrder->getShippingAddress();
        $result = $this->_extractContact($addrMage);
        return $result;
    }

    /**
     * Convert data from Magento format to Odoo API format. Select additional data from DB.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder
     */
    public function getSaleOrder(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder::class);
        /* Collect order data */
        // id_mage
        $orderIdMage = (int)$mageOrder->getId();
        // warehouse_id_odoo
        $warehouseIdOdoo = (int)$this->_extractWarehouseIdOdoo($mageOrder);
        // number
        $number = $mageOrder->getIncrementId();
        // date (will be below)
        // customer
        $customer = $this->getSaleOrderCustomer($mageOrder);
        // addr_billing
        $addrBilling = $this->getAddressBilling($mageOrder);
        // addr_shipping
        $addrShipping = $this->getAddressShipping($mageOrder);
        // price_currency
        $priceCurrency = $mageOrder->getBaseCurrencyCode();
        // pv_total (with date paid)
        $pvOrder = $this->_repoPvSale->getById($orderIdMage);
        $pvTotal = $this->_manFormat->toNumber($pvOrder->getTotal());
        $datePaid = $pvOrder->getDatePaid();
        // lines
        $lines = $this->getSaleOrderLines($mageOrder);
        // shipping
        $shipping = $this->getSaleOrderShipping($mageOrder);
        // payments
        $payments = $this->getSaleOrderPayments($mageOrder);
        /* calculate totals */
        $totals = $this->_getLinesTotals($lines);
        // price_total
        $priceTotal = $totals[self::AMOUNT] + $shipping->getPriceAmountTotal();
        $priceTotal = $this->_manFormat->toNumber($priceTotal);
        // $priceTotal = $this->_manFormat->toNumber($mageOrder->getBaseGrandTotal());
        // price_tax
        $priceTax = $totals[self::TAX] + $shipping->getPriceTaxAmount();
        $priceTax = $this->_manFormat->toNumber($priceTax);
        // $priceTax = $this->_manFormat->toNumber($mageOrder->getBaseTaxAmount());
        // price_discount
        $priceDiscount = $totals[self::DISCOUNT] + $shipping->getPriceDiscount();
        $priceDiscount = $this->_manFormat->toNumber($priceDiscount);
        /* populate Odoo Data Object */
        $result->setIdMage($orderIdMage);
        $result->setWarehouseIdOdoo($warehouseIdOdoo);
        $result->setNumber($number);
        $result->setDatePaid($datePaid);
        $result->setCustomer($customer);
        $result->setAddrBilling($addrBilling);
        $result->setAddrShipping($addrShipping);
        $result->setPriceCurrency($priceCurrency);
        $result->setPriceTotal($priceTotal);
        $result->setPriceTax($priceTax);
        $result->setPriceDiscount($priceDiscount);
        $result->setPvTotal($pvTotal);
        $result->setLines($lines);
        $result->setShipping($shipping);
        $result->setPayments($payments);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Customer
     */
    public function getSaleOrderCustomer(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder\Customer::class);
        /* collect data */
        $custMageId = (int)$mageOrder->getCustomerId();
        $dwnlCust = $this->_repoDwnlCustomer->getById($custMageId);
        $ref = $dwnlCust->getHumanRef();
        $name = $mageOrder->getCustomerName();
        $mageCust = $this->_repoMageCustomer->getById($custMageId);
        $groupCode = $this->_manBusinessCodes->getBusCodeForCustomerGroup($mageCust);
        /* init Odoo data object */
        $result->setIdMage($custMageId);
        $result->setIdMlm($ref);
        $result->setName($name);
        $result->setGroupCode($groupCode);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line[]
     */
    public function getSaleOrderLines(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $lines = [];
        /* collect data */
        $orderId = $mageOrder->getId();
        $storeId = $mageOrder->getStoreId();
        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $aggSaleOrderItems = $this->_repoAggSaleOrderItem->getByOrderAndStock($orderId, $stockId);
        foreach ($aggSaleOrderItems as $item) {
            $productIdOdoo = $item->getOdooIdProduct();
            /* process order line */
            if (isset($lines[$productIdOdoo])) {
                $line = $lines[$productIdOdoo];
            } else {
                $line = $this->_extractLine($item);
            }
            /* process lot for order line ($item is a flat structure - if one sale item consists of 2 lots then
             two entries will be in aggregated results) */
            $lots = $line->getLots();
            $lot = $this->_extractLineLot($item);
            /* save Odoo data object into Odoo line */
            $lots[] = $lot;
            $line->setLots($lots);
            $lines[$productIdOdoo] = $line;
        }
        /* remove keys from array */
        $result = array_values($lines);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\Payment[]
     */
    public function getSaleOrderPayments(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $result = [];
        $odooPayment = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\Payment::class);
        /* collect data */
        $magePayment = $mageOrder->getPayment();
        $code = $this->_manBusinessCodes->getBusCodeForPaymentMethod($magePayment);
        $ordered = $magePayment->getBaseAmountOrdered();
        $amount = $this->_manFormat->toNumber($ordered);
        /* populate Odoo Data Object */
        $odooPayment->setCode($code);
        $odooPayment->setAmount($amount);
        $result[] = $odooPayment;
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Shipping
     */
    public function getSaleOrderShipping(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $result = $this->_manObj->create(\Praxigento\Odoo\Data\Odoo\SaleOrder\Shipping::class);
        /* collect data */
        $code = $this->_manBusinessCodes->getBusCodeForShippingMethod($mageOrder);
        $priceAmount = $mageOrder->getBaseShippingAmount();
        $priceAmount = $this->_manFormat->toNumber($priceAmount);
        $priceDiscount = $mageOrder->getBaseShippingDiscountAmount();
        $priceDiscount = $this->_manFormat->toNumber($priceDiscount);
        $priceTaxAmount = $mageOrder->getBaseShippingTaxAmount();
        $priceTaxAmount = $this->_manFormat->toNumber($priceTaxAmount);
        $priceTaxPercent = $priceTaxAmount / ($priceAmount - $priceDiscount);
        $priceTaxPercent = $this->_manFormat->toNumber($priceTaxPercent, Cfg::ODOO_API_PERCENT_ROUND);
        $priceAmountTotal = ($priceAmount - $priceDiscount) * (1 + $priceTaxPercent);
        $priceAmountTotal = $this->_manFormat->toNumber($priceAmountTotal);
        /* populate Odoo Data Object */
        $result->setCode($code);
        $result->setPriceAmount($priceAmount);
        $result->setPriceDiscount($priceDiscount);
        $result->setPriceTaxPercent($priceTaxPercent);
        $result->setPriceTaxAmount($priceTaxAmount);
        $result->setPriceAmountTotal($priceAmountTotal);
        return $result;
    }
}
