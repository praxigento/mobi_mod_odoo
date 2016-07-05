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
    /** @var \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;
    /** @var \Praxigento\Odoo\Repo\Agg\ISaleOrderItem */
    protected $_repoAggSaleOrderItem;
    /** @var \Praxigento\Pv\Repo\Entity\ISale */
    protected $_repoPvSale;
    /** @var \Praxigento\Pv\Repo\Entity\Sale\IItem */
    protected $_repoPvSaleItem;
    /** @var \Praxigento\Odoo\Repo\Entity\IWarehouse */
    protected $_repoWarehouse;
    /** @var \Praxigento\Warehouse\Repo\Entity\Quantity\ISale */
    protected $_repoWrhsQtySale;
    /** @var \Praxigento\Downline\Repo\Entity\ICustomer */
    protected $_repoDwnlCustomer;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Downline\Repo\Entity\ICustomer $repoDwnlCustomer,
        \Praxigento\Pv\Repo\Entity\ISale $repoPvSale,
        \Praxigento\Pv\Repo\Entity\Sale\IItem $repoPvSaleItem,
        \Praxigento\Warehouse\Repo\Entity\Quantity\ISale $repoWrhsQtySale,
        \Praxigento\Odoo\Repo\Agg\ISaleOrderItem $repoAggSaleOrderItem,
        \Praxigento\Odoo\Repo\Entity\IWarehouse $repoWarehouse
    ) {
        $this->_manStock = $manStock;
        $this->_repoDwnlCustomer = $repoDwnlCustomer;
        $this->_repoPvSale = $repoPvSale;
        $this->_repoPvSaleItem = $repoPvSaleItem;
        $this->_repoWrhsQtySale = $repoWrhsQtySale;
        $this->_repoAggSaleOrderItem = $repoAggSaleOrderItem;
        $this->_repoWarehouse = $repoWarehouse;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $mageItem
     * @param \Praxigento\Odoo\Data\Agg\SaleOrderItem[] $saleOrderItems
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line
     */
    public function getOdooLineFormMageItem(
        \Magento\Sales\Api\Data\OrderItemInterface $mageItem,
        $saleOrderItems
    ) {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line();
        /* load related data */

        /* collect data */
        $productId = 11;
        $qty = 21;
        $lots = [];
        $priceAct = 21;
        $priceAdj = 12;
        $priceDiscount = 43;
        $pvAct = 43;
        $pvDiscount = 43;

        /* set data */
        $result->setPriceActual();
        return $result;
    }

    public function getOdooLotFormMageQtySale()
    {
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Customer
     */
    public function getSaleOrderCustomer($mageOrder)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Customer();
        /* collect data */
        $custMageId = $mageOrder->getCustomerId();
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
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\Contact
     */
    public function getAddressBilling($mageOrder)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\Contact();
        /* collect data */
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $addrMage */
        $addrMage = $mageOrder->getBillingAddress();
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
        $result->setName($name);
        $result->setPhone($phone);
        $result->setEmail($email);
        $result->setCountry($country);
        $result->setState($state);
        $result->setCity($city);
        $result->setStreet($street);
        $result->setZip($zip);
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
        $orderIdMage = $mageOrder->getId();
        // warehouse_id_odoo
        $storeId = $mageOrder->getStoreId();
        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
        $warehouse = $this->_repoWarehouse->getById($stockId);
        $warehouseIdOdoo = $warehouse->getOdooRef();
        // number
        $number = $mageOrder->getIncrementId();
        // date (will be below)
        // customer
        $customer = $this->getSaleOrderCustomer($mageOrder);
        // addr_billing
        $addrBilling = $this->getAddressBilling($mageOrder);
        // addr_shipping
        $addrShipping = '';
        // shipping_method
        $shippingMethod = '';
        // price_shipping
        $priceShipping = $mageOrder->getBaseShippingInvoiced(); // TODO: leave one only shipping price
        $priceShipping = $mageOrder->getBaseShippingInclTax();
        // price_discount_additional
        $priceDiscountAdditional = '';
        // price_tax
        $priceTax = '';
        // price_order_total
        $priceOrderTotal = '';
        // pv_order_total (with date)
        $pvOrder = $this->_repoPvSale->getById($orderIdMage);
        $pvOrderTotal = $pvOrder->getTotal();
        $datePaid = $pvOrder->getDatePaid();
        // lines
        $aggSaleOrderItems = $this->_repoAggSaleOrderItem->getByOrderAndStock($orderIdMage, $stockId);
        $lines = [];
        foreach ($aggSaleOrderItems as $item) {
            $productIdOdoo = $item->getOdooIdProduct();
            /* process order line */
            if (isset($lines[$productIdOdoo])) {
                $line = $lines[$productIdOdoo];
            } else {
                $line = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line();
                $line->setProductIdOdoo($productIdOdoo);
                $line->setQty($item->getItemQty());
                $line->setLots([]);
                $line->setPriceActual($item->getPrice());
                $line->setPriceAdjusted($item->getPrice());
                $line->setPriceDiscount($item->getItemDiscountPrice());
                $line->setPvActual(9999.99);
                $line->setPvDiscount($item->getPvDiscount());
            }
            /* process lots for order line */
            $lots = $line->getLots();
            $lot = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line\Lot();
            $lot->setIdOdoo($item->getOdooIdLot());
            $lot->setQuantity($item->getLotQty());
            /* save results into line */
            $lots[] = $lot;
            $line->setLots($lots);
            $lines[$productIdOdoo] = $line;
        }
        // payments
        $payments = '';

        $priceDiscountTotal = $mageOrder->getBaseDiscountInvoiced();
        $priceDiscountItems = 0;
        $priceTax = $mageOrder->getBaseTaxInvoiced();


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
}