<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale\Order;

use Praxigento\Odoo\Config as Cfg;

/**
 * Collect order data and compose Odoo compatible data object.
 */
class Collector
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
    /** @var \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Lots\Get\Builder */
    protected $qbLots;
    /** @var \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Get\Builder */
    protected $qbOrderItems;
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
        \Praxigento\Odoo\Repo\Entity\IWarehouse $repoWarehouse,
        \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Get\Builder $qbOrderItems,
        \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Lots\Get\Builder $qbLots
    )
    {
        $this->manObj = $manObj;
        $this->manStock = $manStock;
        $this->manBusinessCodes = $manBusinessCodes;
        $this->manFormat = $manFormat;
        $this->repoCustomer = $repoCustomer;
        $this->repoDwnlCustomer = $repoDwnlCustomer;
        $this->repoPvSale = $repoPvSale;
        $this->repoPvSaleItem = $repoPvSaleItem;
        $this->repoWrhsQtySale = $repoWrhsQtySale;
        $this->repoWarehouse = $repoWarehouse;
        $this->qbOrderItems = $qbOrderItems;
        $this->qbLots = $qbLots;
    }

    /**
     * Get magento data for lots related to order item to be converted into Odoo format.
     *
     * @param $itemId
     * @return \Flancer32\Lib\Data[]
     */
    protected function dbGetLots($itemId)
    {
        $result = [];
        $query = $this->qbLots->build();
        $conn = $query->getConnection();
        $bind = [
            $this->qbLots::BIND_SALE_ITEM_ID => $itemId
        ];
        $rows = $conn->fetchAll($query, $bind);
        foreach ($rows as $row) {
            $data = new \Flancer32\Lib\Data($row);
            $result[] = $data;
        }
        return $result;
    }

    /**
     * Get magento data for order items to be converted into Odoo format.
     *
     * @param $orderId
     * @return \Flancer32\Lib\Data[]
     */
    protected function dbGetOrderItems($orderId)
    {
        $result = [];
        $query = $this->qbOrderItems->build();
        $conn = $query->getConnection();
        $bind = [
            $this->qbOrderItems::BIND_ORDER_ID => $orderId
        ];
        $rows = $conn->fetchAll($query, $bind);
        foreach ($rows as $row) {
            $data = new \Flancer32\Lib\Data($row);
            $itemId = $row[$this->qbOrderItems::A_ITEM_ID];
            $result[$itemId] = $data;
        }
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $addrMage
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    protected function extractContact(\Magento\Sales\Api\Data\OrderAddressInterface $addrMage)
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
     * Get data from DB query and compose initial Odoo data object,
     *
     * @param \Flancer32\Lib\Data $item
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line
     */
    protected function extractLine(\Flancer32\Lib\Data $item)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line();
        /* collect data */
        $productIdOdoo = (int)$item->get($this->qbOrderItems::A_ODOO_REF);
        $qtyLine = $item->get($this->qbOrderItems::A_QTY_ORDERED);
        $qtyLine = $this->manFormat->toNumber($qtyLine);
        /* price related attributes */
        $priceSaleUnit = $item->get($this->qbOrderItems::A_BASE_PRICE);
        $priceSaleUnit = $this->manFormat->toNumber($priceSaleUnit);
        $priceDiscountLine = 0; // to be calculated later
        $taxPercent = $item->get($this->qbOrderItems::A_TAX_PERCENT);
        $taxPercent = $taxPercent / 100;
        $priceTaxPercent = $this->manFormat->toNumber($taxPercent, Cfg::ODOO_API_PERCENT_ROUND);
        $priceTotalLine = $item->get($this->qbOrderItems::A_BASE_ROW_TOTAL_INCL_TAX);
        $priceTotalLine = $this->manFormat->toNumber($priceTotalLine);
        $priceTaxLine = 0;  // to be calculated later
        /* PV attributes */
        $pvSubtotal = $item->get($this->qbOrderItems::A_PV_SUBTOTAL);
        $pvSaleUnit = ($pvSubtotal / $qtyLine);
        $pvSaleUnit = $this->manFormat->toNumber($pvSaleUnit);
        $pvDiscountLine = abs($item->get($this->qbOrderItems::A_PV_DISCOUNT));
        $pvDiscountLine = $this->manFormat->toNumber($pvDiscountLine);
        /* init Odoo data object */
        $result->setProductIdOdoo($productIdOdoo);
        $result->setQtyLine($qtyLine);
        $result->setLots([]); // will be used later
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
     * Convert DB data into Odoo API data.
     *
     * @param \Flancer32\Lib\Data[] $lotsData
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot[]
     */
    protected function extractLineLots($lotsData)
    {
        $result = [];
        foreach ($lotsData as $one) {
            $lot = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot();
            /* Lot's ID in Odoo */
            $idOdoo = (int)$one->get($this->qbLots::A_ODOO_ID);
            if ($idOdoo != Cfg::NULL_LOT_ID) $lot->setIdOdoo($idOdoo);
            /* qty in this lot */
            $qty = $one->get($this->qbLots::A_TOTAL);
            $qty = $this->manFormat->toNumber($qty);
            $lot->setQty($qty);
            $result[] = $lot;
        }
        return $result;
    }

    /**
     * Convert Magento's $storeId to Odoo's $warehouseId.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return int
     */
    protected function extractWarehouseIdOdoo(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $storeId = $mageOrder->getStoreId();
        $stockId = $this->manStock->getStockIdByStoreId($storeId);
        $warehouse = $this->repoWarehouse->getById($stockId);
        $result = $warehouse->getOdooRef();
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    protected function getAddressBilling(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $addrMage */
        $addrMage = $mageOrder->getBillingAddress();
        $result = $this->extractContact($addrMage);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    protected function getAddressShipping(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $addrMage */
        $addrMage = $mageOrder->getShippingAddress();
        $result = $this->extractContact($addrMage);
        return $result;
    }

    /**
     * Calculates totals for all lines (amount, discount, tax).
     *
     * @param \Praxigento\Odoo\Data\Odoo\SaleOrder\Line[] $lines
     * @return array
     */
    protected function getLinesTotals($lines)
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
     * Convert Magento Order to Odoo API data.
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
        $warehouseIdOdoo = (int)$this->extractWarehouseIdOdoo($mageOrder);
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
        $totals = $this->getLinesTotals($lines);
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
        /* calculate prices & taxes according to Odoo formula */
        $this->calcTaxes($result);
        return $result;
    }

    /**
     * Re-calculate taxes according Odoo formula.
     *
     * https://confluence.prxgt.com/x/BYBoB
     *
     * @param \Praxigento\Odoo\Data\Odoo\SaleOrder $order
     */
    protected function calcTaxes(\Praxigento\Odoo\Data\Odoo\SaleOrder $order)
    {
        /* calculate really paid amount */
        $payments = $order->getPayments();
        $paid = 0;
        foreach ($payments as $payment) {
            $paid += $payment->getAmount();
        }
        /* calculate lines summary */
        $lines = $order->getLines();
        $totalLines = 0;
        foreach ($lines as $line) {
            $total = $line->getPriceTotalLine();
            $totalLines += $total;
        }
        /* get $k coefficient */
        $k = $paid / $totalLines;
        /* fix all lines */
        foreach ($lines as $line) {
            /* base data for calculation */
            $qty = $line->getQtyLine();
            $unitPrice = $line->getPriceSaleUnit();
            $taxPercent = $line->getPriceTaxPercent();
            $totalLine = $line->getPriceTotalLine();
            /* calc fixed values */
            $fixedTotal = $totalLine * $k;
            $fixedTotal = $this->manFormat->toNumber($fixedTotal);
            $woDiscountTotal = $qty * $unitPrice * (1 + $taxPercent);
            $fixedDiscountWithTax = $woDiscountTotal - $fixedTotal;
            $fixedDiscount = $fixedDiscountWithTax / (1 + $taxPercent);
            $fixedDiscount = $this->manFormat->toNumber($fixedDiscount);
            $fixedTaxAmount = ($qty * $unitPrice - $fixedDiscount) * $taxPercent;
            $fixedTaxAmount = $this->manFormat->toNumber($fixedTaxAmount);
            /* put fixed values back to $line */
            $line->setPriceDiscountLine($fixedDiscount);
            $line->setPriceTaxLine($fixedTaxAmount);
            $line->setPriceTotalLine($fixedTotal);
        }
    }
    /**
     * Extract Odoo compatible customer data from Magento order.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Customer
     */
    protected function getSaleOrderCustomer(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
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
    protected function getSaleOrderLines(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $lines = [];
        /* collect data */
        $orderId = $mageOrder->getId();
        $dbDataItems = $this->dbGetOrderItems($orderId);
        foreach ($dbDataItems as $item) {
            $productIdOdoo = $item->get($this->qbOrderItems::A_ODOO_REF);
            $itemId = $item->get($this->qbOrderItems::A_ITEM_ID);
            /* process order line */
            $line = $this->extractLine($item);
            /* request lots data for the sale item */
            $dbDataLots = $this->dbGetLots($itemId);
            $lots = $this->extractLineLots($dbDataLots);
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
    protected function getSaleOrderPayments(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
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
    protected function getSaleOrderShipping(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
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
        if (($priceAmount + $priceDiscount + $priceTaxAmount) < Cfg::DEF_ZERO) {
            /* free shipping */
            $priceTaxPercent = 0;
        } else {
            $priceTaxPercent = $priceTaxAmount / ($priceAmount - $priceDiscount);
        }
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