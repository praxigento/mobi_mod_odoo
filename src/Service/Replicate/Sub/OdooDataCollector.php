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
    protected $manBusinessCodes;
    /** @var  \Praxigento\Core\Tool\IFormat */
    protected $manFormat;
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var \Praxigento\Warehouse\Tool\IStockManager */
    protected $manStock;
    /** @var \Praxigento\Odoo\Repo\Agg\ISaleOrderItem */
    protected $repoAggSaleOrderItem;
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $repoCustomer;
    /** @var \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $repoDwnlCustomer;
    /** @var \Praxigento\Pv\Repo\Entity\ISale */
    protected $repoPvSale;
    /** @var \Praxigento\Pv\Repo\Entity\Sale\IItem */
    protected $repoPvSaleItem;
    /** @var \Praxigento\Odoo\Repo\Entity\IWarehouse */
    protected $repoWarehouse;
    /** @var \Praxigento\Warehouse\Repo\Entity\Quantity\ISale */
    protected $repoWrhsQtySale;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Odoo\Tool\IBusinessCodesManager $manBusinessCodes,
        \Praxigento\Core\Tool\IFormat $manFormat,
        \Magento\Customer\Api\CustomerRepositoryInterface $repoCustomer,
        \Praxigento\Downline\Repo\Entity\ICustomer $repoDwnlCustomer,
        \Praxigento\Pv\Repo\Entity\ISale $repoPvSale,
        \Praxigento\Pv\Repo\Entity\Sale\IItem $repoPvSaleItem,
        \Praxigento\Warehouse\Repo\Entity\Quantity\ISale $repoWrhsQtySale,
        \Praxigento\Odoo\Repo\Agg\ISaleOrderItem $repoAggSaleOrderItem,
        \Praxigento\Odoo\Repo\Entity\IWarehouse $repoWarehouse
    ) {
        $this->manObj = $manObj;
        $this->manStock = $manStock;
        $this->manBusinessCodes = $manBusinessCodes;
        $this->manFormat = $manFormat;
        $this->repoCustomer = $repoCustomer;
        $this->repoDwnlCustomer = $repoDwnlCustomer;
        $this->repoPvSale = $repoPvSale;
        $this->repoPvSaleItem = $repoPvSaleItem;
        $this->repoWrhsQtySale = $repoWrhsQtySale;
        $this->repoAggSaleOrderItem = $repoAggSaleOrderItem;
        $this->repoWarehouse = $repoWarehouse;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $addrMage
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    public function _extractContact(\Magento\Sales\Api\Data\OrderAddressInterface $addrMage)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\Contact();
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
     * @param \Praxigento\Odoo\Repo\Agg\Data\SaleOrderItem $item
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line
     */
    public function _extractLine(\Praxigento\Odoo\Repo\Agg\Data\SaleOrderItem $item)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line();
        /* collect data */
        $productIdOdoo = (int)$item->getOdooIdProduct();
        $qtyLine = $this->manFormat->toNumber($item->getItemQty());
        /* price attributes */
        $priceSaleUnit = $this->manFormat->toNumber($item->getPriceUnitOrig());
        $priceDiscountLine = abs($this->manFormat->toNumber($item->getPriceDiscount()));
        /* price related calculated attributes */
        $priceTaxPercent = $this->manFormat->toNumber(
            $item->getPriceTaxPercent() / 100,
            Cfg::ODOO_API_PERCENT_ROUND
        );
        $priceTotalLine = ($qtyLine * $priceSaleUnit - $priceDiscountLine) * (1 + $priceTaxPercent);
        $priceTotalLine = $this->manFormat->toNumber($priceTotalLine);
        $priceTaxLine = $priceTotalLine / (1 + $priceTaxPercent) * $priceTaxPercent;
        $priceTaxLine = $this->manFormat->toNumber($priceTaxLine);
        /* PV attributes */
        $pvSaleUnit = $this->manFormat->toNumber($item->getPvUnit());
        $pvDiscountLine = abs($this->manFormat->toNumber($item->getPvDiscount()));
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
     * @param \Praxigento\Odoo\Repo\Agg\Data\SaleOrderItem $item
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot
     */
    public function _extractLineLot(\Praxigento\Odoo\Repo\Agg\Data\SaleOrderItem $item)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot();
        $idOdoo = (int)$item->getOdooIdLot();
        $qty = $this->manFormat->toNumber($item->getLotQty());
        if ($idOdoo != \Praxigento\Odoo\Repo\Agg\Data\Lot::NULL_LOT_ID) $result->setIdOdoo($idOdoo);
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
        $stockId = $this->manStock->getStockIdByStoreId($storeId);
        $warehouse = $this->repoWarehouse->getById($stockId);
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
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder();
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
        $pvOrder = $this->repoPvSale->getById($orderIdMage);
        $pvTotal = $this->manFormat->toNumber($pvOrder->getTotal());
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
        $priceTotal = $this->manFormat->toNumber($priceTotal);
        // $priceTotal = $this->_manFormat->toNumber($mageOrder->getBaseGrandTotal());
        // price_tax
        $priceTax = $totals[self::TAX] + $shipping->getPriceTaxAmount();
        $priceTax = $this->manFormat->toNumber($priceTax);
        // $priceTax = $this->_manFormat->toNumber($mageOrder->getBaseTaxAmount());
        // price_discount
        $priceDiscount = $totals[self::DISCOUNT] + $shipping->getPriceDiscount();
        $priceDiscount = $this->manFormat->toNumber($priceDiscount);
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
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Customer();
        /* collect data */
        $custMageId = (int)$mageOrder->getCustomerId();
        $dwnlCust = $this->repoDwnlCustomer->getById($custMageId);
        $ref = $dwnlCust->getHumanRef();
        $name = $mageOrder->getCustomerName();
        $mageCust = $this->repoCustomer->getById($custMageId);
        $groupId = $mageCust->getGroupId();
        $groupCode = $this->manBusinessCodes->getBusCodeForCustomerGroupById($groupId);
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
        $stockId = $this->manStock->getStockIdByStoreId($storeId);
        $aggSaleOrderItems = $this->repoAggSaleOrderItem->getByOrderAndStock($orderId, $stockId);
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
        $odooPayment = new \Praxigento\Odoo\Data\Odoo\Payment();
        /* collect data */
        $magePayment = $mageOrder->getPayment();
        $code = $this->manBusinessCodes->getBusCodeForPaymentMethod($magePayment);
        $ordered = $magePayment->getBaseAmountOrdered();
        $amount = $this->manFormat->toNumber($ordered);
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
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Shipping();
        /* collect data */
        $code = $this->manBusinessCodes->getBusCodeForShippingMethod($mageOrder);
        $priceAmount = $mageOrder->getBaseShippingAmount();
        $priceAmount = $this->manFormat->toNumber($priceAmount);
        $priceDiscount = $mageOrder->getBaseShippingDiscountAmount();
        $priceDiscount = $this->manFormat->toNumber($priceDiscount);
        $priceTaxAmount = $mageOrder->getBaseShippingTaxAmount();
        $priceTaxAmount = $this->manFormat->toNumber($priceTaxAmount);
        $priceTaxPercent = $priceTaxAmount / ($priceAmount - $priceDiscount);
        $priceTaxPercent = $this->manFormat->toNumber($priceTaxPercent, Cfg::ODOO_API_PERCENT_ROUND);
        $priceAmountTotal = ($priceAmount - $priceDiscount) * (1 + $priceTaxPercent);
        $priceAmountTotal = $this->manFormat->toNumber($priceAmountTotal);
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
