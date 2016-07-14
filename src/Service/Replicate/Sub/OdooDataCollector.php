<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Sub;

/**
 * Extract data from Magento Sales Order and collect additional data to compose Odoo Sales Order.
 */
class OdooDataCollector
{
    /** @var  \Praxigento\Odoo\Tool\IBusinessCodesManager */
    protected $_manBusinessCodes;
    /** @var \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;
    /** @var \Praxigento\Odoo\Repo\Agg\ISaleOrderItem */
    protected $_repoAggSaleOrderItem;
    /** @var \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $_repoDwnlCustomer;
    /** @var \Praxigento\Pv\Repo\Entity\ISale */
    protected $_repoPvSale;
    /** @var \Praxigento\Pv\Repo\Entity\Sale\IItem */
    protected $_repoPvSaleItem;
    /** @var \Praxigento\Odoo\Repo\Entity\IWarehouse */
    protected $_repoWarehouse;
    /** @var \Praxigento\Warehouse\Repo\Entity\Quantity\ISale */
    protected $_repoWrhsQtySale;
    /** @var  \Praxigento\Core\Tool\IFormat */
    protected $_manFormat;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Downline\Repo\Entity\ICustomer $repoDwnlCustomer,
        \Praxigento\Pv\Repo\Entity\ISale $repoPvSale,
        \Praxigento\Pv\Repo\Entity\Sale\IItem $repoPvSaleItem,
        \Praxigento\Warehouse\Repo\Entity\Quantity\ISale $repoWrhsQtySale,
        \Praxigento\Odoo\Repo\Agg\ISaleOrderItem $repoAggSaleOrderItem,
        \Praxigento\Odoo\Repo\Entity\IWarehouse $repoWarehouse,
        \Praxigento\Odoo\Tool\IBusinessCodesManager $manBusinessCodes,
        \Praxigento\Core\Tool\IFormat $manFormat
    ) {
        $this->_manStock = $manStock;
        $this->_repoDwnlCustomer = $repoDwnlCustomer;
        $this->_repoPvSale = $repoPvSale;
        $this->_repoPvSaleItem = $repoPvSaleItem;
        $this->_repoWrhsQtySale = $repoWrhsQtySale;
        $this->_repoAggSaleOrderItem = $repoAggSaleOrderItem;
        $this->_repoWarehouse = $repoWarehouse;
        $this->_manBusinessCodes = $manBusinessCodes;
        $this->_manFormat = $manFormat;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $addrMage
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    protected function _extractContact(\Magento\Sales\Api\Data\OrderAddressInterface $addrMage)
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
     * @param \Praxigento\Odoo\Data\Agg\SaleOrderItem $item
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line
     */
    protected function _extractLine(\Praxigento\Odoo\Data\Agg\SaleOrderItem $item)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line();
        /* collect data */
        $productIdOdoo = (int)$item->getOdooIdProduct();
        $qtyLine = $this->_manFormat->toNumber($item->getItemQty());
        $priceSaleUnit = $this->_manFormat->toNumber($item->getPriceUnit());
        $priceDiscountLine = $this->_manFormat->toNumber($item->getPriceDiscount());
        $pvSaleUnit = $this->_manFormat->toNumber($item->getPvUnit());
        $pvDiscountLine = $this->_manFormat->toNumber($item->getPvDiscount());
        /* init Odoo data object */
        $result->setProductIdOdoo($productIdOdoo);
        $result->setQtyLine($qtyLine);
        $result->setLots([]); // will be initialized later
        $result->setPriceSaleUnit($priceSaleUnit);
        $result->setPriceDiscountLine($priceDiscountLine);
        $result->setPvSaleUnit($pvSaleUnit);
        $result->setPvDiscountLine($pvDiscountLine);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Data\Agg\SaleOrderItem $item
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot
     */
    protected function _extractLineLot(\Praxigento\Odoo\Data\Agg\SaleOrderItem $item)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot();
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
    protected function _extractWarehouseIdOdoo(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $storeId = $mageOrder->getStoreId();
        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $warehouse = $this->_repoWarehouse->getById($stockId);
        $result = $warehouse->getOdooRef();
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder $mageOrder
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
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    public function getAddressShipping(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\Contact();
        /* collect data */
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
        // shipping_method
        $shippingMethod = $this->_manBusinessCodes->getShippingMethodCode($mageOrder);
        // price_shipping
        $priceShipping = $this->_manFormat->toNumber($mageOrder->getBaseShippingInclTax());
        // price_discount_additional
        $priceDiscountAdditional = $this->_manFormat->toNumber($mageOrder->getBaseDiscountAmount());
        // price_tax
        $priceTax = $this->_manFormat->toNumber($mageOrder->getBaseTaxAmount());
        // price_order_total
        $priceOrderTotal = $this->_manFormat->toNumber($mageOrder->getBaseGrandTotal());
        // pv_order_total (with date)
        $pvOrder = $this->_repoPvSale->getById($orderIdMage);
        $pvOrderTotal = $this->_manFormat->toNumber($pvOrder->getTotal());
        $datePaid = $pvOrder->getDatePaid();
        // lines
        $lines = $this->getSaleOrderLines($mageOrder);
        // payments
        $payments = $this->getSaleOrderPayments($mageOrder);
        /* populate Odoo Data Object */
        $result->setIdMage($orderIdMage);
        $result->setWarehouseIdOdoo($warehouseIdOdoo);
        $result->setNumber($number);
        $result->setDate($datePaid);
        $result->setCustomer($customer);
        $result->setAddrBilling($addrBilling);
        $result->setAddrShipping($addrShipping);
        $result->setShippingMethod($shippingMethod);
        $result->setPriceShipping($priceShipping);
        $result->setPriceDiscountAdditional($priceDiscountAdditional);
        $result->setPriceTax($priceTax);
        $result->setPriceOrderTotal($priceOrderTotal);
        $result->setPvOrderTotal($pvOrderTotal);
        $result->setLines($lines);
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
        $dwnlCust = $this->_repoDwnlCustomer->getById($custMageId);
        $ref = $dwnlCust->getHumanRef();
        $name = $mageOrder->getCustomerName();
        /* init Odoo data object */
        $result->setIdMage($custMageId);
        $result->setIdMlm($ref);
        $result->setName($name);
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
        $odooPayment = new \Praxigento\Odoo\Data\Odoo\Payment();
        /* collect data */
        $magePayment = $mageOrder->getPayment();
        $code = $this->_manBusinessCodes->getPaymentMethodCode($magePayment);
        $ordered = $magePayment->getBaseAmountOrdered();
        $amount = $this->_manFormat->toNumber($ordered);
        /* populate Odoo Data Object */
        $odooPayment->setCode($code);
        $odooPayment->setAmount($amount);
        $result[] = $odooPayment;
        return $result;
    }
}