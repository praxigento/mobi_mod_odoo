<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale\Order\A;

use Praxigento\Odoo\Config as Cfg;

/**
 * Collect order data and compose Odoo compatible data object.
 */
class Collector
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    private $daoCustomer;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    private $daoDwnlCustomer;
    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    private $daoGeneric;
    /** @var \Praxigento\Odoo\Repo\Dao\Product */
    private $daoOdooProd;
    /** @var \Praxigento\Pv\Repo\Dao\Sale */
    private $daoPvSale;
    /** @var \Praxigento\Pv\Repo\Dao\Sale\Item */
    private $daoPvSaleItem;
    /** @var \Praxigento\Wallet\Repo\Dao\Partial\Sale */
    private $daoWalletSale;
    /** @var \Praxigento\Odoo\Repo\Dao\Warehouse */
    private $daoWarehouse;
    /** @var \Magento\Sales\Api\Data\OrderPaymentFactory */
    private $factPayment;
    /** @var  \Praxigento\Core\Api\Helper\Format */
    private $hlpFormat;
    /** @var \Praxigento\Warehouse\Api\Helper\Stock */
    private $hlpStock;
    /** @var  \Praxigento\Odoo\Api\Helper\BusinessCodes */
    private $manBusinessCodes;
    /** @var \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Lots\Get\Builder */
    private $qbLots;
    /** @var \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Tax\Item\Get\Builder */
    private $qbTaxItems;

    public function __construct(
        \Magento\Sales\Model\Order\PaymentFactory $factPayment,
        \Praxigento\Warehouse\Api\Helper\Stock $hlpStock,
        \Praxigento\Odoo\Api\Helper\BusinessCodes $hlpBusinessCodes,
        \Praxigento\Core\Api\Helper\Format $hlpFormat,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric,
        \Magento\Customer\Api\CustomerRepositoryInterface $daoCustomer,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCustomer,
        \Praxigento\Pv\Repo\Dao\Sale $daoPvSale,
        \Praxigento\Pv\Repo\Dao\Sale\Item $daoPvSaleItem,
        \Praxigento\Odoo\Repo\Dao\Warehouse $daoWarehouse,
        \Praxigento\Odoo\Repo\Dao\Product $daoOdooProd,
        \Praxigento\Wallet\Repo\Dao\Partial\Sale $daoWalletSale,
        \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Items\Lots\Get\Builder $qbLots,
        \Praxigento\Odoo\Repo\Query\Replicate\Sale\Orders\Tax\Item\Get\Builder $qbTaxItems
    ) {
        $this->factPayment = $factPayment;
        $this->hlpStock = $hlpStock;
        $this->manBusinessCodes = $hlpBusinessCodes;
        $this->hlpFormat = $hlpFormat;
        $this->daoGeneric = $daoGeneric;
        $this->daoCustomer = $daoCustomer;
        $this->daoDwnlCustomer = $daoDwnlCustomer;
        $this->daoPvSale = $daoPvSale;
        $this->daoPvSaleItem = $daoPvSaleItem;
        $this->daoWarehouse = $daoWarehouse;
        $this->daoOdooProd = $daoOdooProd;
        $this->daoWalletSale = $daoWalletSale;
        $this->qbLots = $qbLots;
        $this->qbTaxItems = $qbTaxItems;
    }

    /**
     * Get total PV for Sale Order Item.
     *
     * @param int $itemId Sale Order Item ID
     * @return float Total PV for the Sale Order Item
     */
    private function dbGetItemPvTotal($itemId)
    {
        /** @var \Praxigento\Pv\Repo\Data\Sale\Item $data */
        $data = $this->daoPvSaleItem->getById($itemId);
        $result = $data->getTotal();
        return $result;
    }

    /**
     * Get all taxes for the Sale Item by item ID (Magento ID).
     *
     * @param int $itemId
     * @return \Praxigento\Core\Data[]
     */
    private function dbGetItemTaxes($itemId)
    {
        $result = [];
        $entity = Cfg::ENTITY_MAGE_SALES_ORDER_TAX_ITEM;
        $where = Cfg::E_SALE_ORDER_TAX_ITEM_A_ITEM_ID . '=' . (int)$itemId;
        $rows = $this->daoGeneric->getEntities($entity, '*', $where);
        foreach ($rows as $row) {
            $data = new \Praxigento\Core\Data($row);
            $result[] = $data;
        }
        return $result;
    }

    /**
     * Get magento data for lots related to order item to be converted into Odoo format.
     *
     * @param $itemId
     * @return \Praxigento\Core\Data[]
     */
    private function dbGetLots($itemId)
    {
        $result = [];
        $query = $this->qbLots->build();
        $conn = $query->getConnection();
        $bind = [
            $this->qbLots::BIND_SALE_ITEM_ID => $itemId
        ];
        $rows = $conn->fetchAll($query, $bind);
        foreach ($rows as $row) {
            $data = new \Praxigento\Core\Data($row);
            $result[] = $data;
        }
        return $result;
    }

    /**
     * Get all taxes rates for sale order by order ID.
     *
     * @param $saleId
     * @return \Praxigento\Core\Data[]
     */
    private function dbGetOrderTaxes($saleId)
    {
        $result = [];
        $entity = Cfg::ENTITY_MAGE_SALES_ORDER_TAX;
        $where = Cfg::E_SALE_ORDER_TAX_A_ORDER_ID . '=' . (int)$saleId;
        $rows = $this->daoGeneric->getEntities($entity, null, $where);
        foreach ($rows as $row) {
            $data = new \Praxigento\Core\Data($row);
            $result[] = $data;
        }
        return $result;
    }

    /**
     * Get tax rates for shipping by sale order ID.
     *
     * @param int $saleId
     * @return \Praxigento\Core\Data[]
     */
    private function dbGetShippingTaxes($saleId)
    {
        $result = [];
        /* get base query */
        $query = $this->qbTaxItems->build();
        /* add conditions */
        $bindType = 'taxType';
        $cond = $this->qbTaxItems::AS_ITEM_TAX . '.' . Cfg::E_SALE_ORDER_TAX_ITEM_A_TAXABLE_ITEM_TYPE
            . '=:' . $bindType;
        $query->where($cond);
        /* map query parameters */
        $bind = [
            $this->qbTaxItems::BIND_ORDER_ID => $saleId,
            $bindType => \Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector::ITEM_TYPE_SHIPPING
        ];
        /* execute query */
        $conn = $query->getConnection();
        $rows = $conn->fetchAll($query, $bind);
        foreach ($rows as $row) {
            $data = new \Praxigento\Core\Data($row);
            $result[] = $data;
        }
        return $result;
    }

    /**
     * Get tax rate code by tax rate ID.
     *
     * @param int $taxId
     * @return string|null
     */
    private function dbGetTaxCodeByTaxId($taxId)
    {
        $result = null;
        $entity = Cfg::ENTITY_MAGE_SALES_ORDER_TAX;
        $where = Cfg::E_SALE_ORDER_TAX_A_TAX_ID . '=' . (int)$taxId;
        $rows = $this->daoGeneric->getEntities($entity, '*', $where);
        if (is_array($rows)) {
            /* one only row should present in result set */
            $row = reset($rows);
            $result = $row[Cfg::E_SALE_ORDER_TAX_A_CODE];
        }
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Contact
     */
    private function getAddressBilling(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $addrMage */
        $addrMage = $sale->getBillingAddress();
        $result = $this->getContact($addrMage);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Contact
     */
    private function getAddressShipping(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $addrMage */
        $addrMage = $sale->getShippingAddress();
        $result = $this->getContact($addrMage);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $addr
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Contact
     */
    private function getContact(\Magento\Sales\Api\Data\OrderAddressInterface $addr)
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Contact();
        /* collect data */
        $name = $addr->getName();
        $phone = $addr->getTelephone();
        $email = $addr->getEmail();
        $country = $addr->getCountryId();
        $state = $addr->getRegionCode();
        $city = $addr->getCity();
        $street = $addr->getStreet(); // street data is array
        $street = implode('', $street);
        $zip = $addr->getPostcode();
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
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line\Tax
     * @throws \Exception
     */
    private function getItemTax(\Magento\Sales\Api\Data\OrderItemInterface $item)
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line\Tax();
        $itemMageId = $item->getItemId();
        /* collect data */
        $rates = $this->getItemTaxRates($itemMageId);
        $rate = reset($rates);
        if ($rate) {
            $base = $rate->getAmount() / $rate->getPercent();
            $base = $this->hlpFormat->toNumber($base);
        } else {
            /* ad fake zero-node to result (SAN-581) */
            $priceBase = $item->getBasePrice();
            $qty = $item->getQtyOrdered();
            $price = $priceBase / $qty;
            $base = round($price, 2);
            $rate = new  \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate();
            $rate->setCode('SAN-581');
            $rate->setPercent(0);
            $rate->setAmount(0);
            $rates = [$rate];
        }
        /* populate Odoo Data Object */
        $result->setBase($base);
        $result->setRates($rates);
        return $result;
    }

    /**
     * @param int $itemId
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate[]
     */
    private function getItemTaxRates($itemId)
    {
        $result = [];
        $rates = $this->dbGetItemTaxes($itemId);
        foreach ($rates as $rate) {
            /* collect data */
            $taxId = $rate->get(Cfg::E_SALE_ORDER_TAX_ITEM_A_TAX_ID);
            $code = $this->dbGetTaxCodeByTaxId($taxId);
            $percent = $rate->get(Cfg::E_SALE_ORDER_TAX_ITEM_A_TAX_PERCENT);
            $percent /= 100;
            $percent = $this->hlpFormat->toNumber($percent, Cfg::ODOO_API_PERCENT_ROUND);
            $amount = $rate->get(Cfg::E_SALE_ORDER_TAX_ITEM_A_REAL_BASE_AMOUNT);
            $amount = $this->hlpFormat->toNumber($amount);
            /* init Odoo data object */
            $data = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate();
            $data->setCode($code);
            $data->setAmount($amount);
            $data->setPercent($percent);
            $result[] = $data;
        }
        return $result;
    }

    /**
     * Extract Odoo compatible customer data from Magento order.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Customer
     */
    private function getOrderCustomer(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Customer();
        /* collect data */
        $custMageId = (int)$sale->getCustomerId();
        $dwnlCust = $this->daoDwnlCustomer->getById($custMageId);
        $ref = $dwnlCust->getMlmId();
        $mageCust = $this->daoCustomer->getById($custMageId);
        $nameFirst = $mageCust->getFirstname();
        $nameLast = $mageCust->getLastname();
        $name = "$nameFirst $nameLast";
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
     * Extract data from magento model and compose initial Odoo data object,
     *
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line
     */
    private function getOrderLine(\Magento\Sales\Api\Data\OrderItemInterface $item)
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line();
        /* collect data */
        $itemIdMage = $item->getId();
        $productIdMage = $item->getProductId();
        $productIdOdoo = (int)$this->daoOdooProd->getOdooIdByMageId($productIdMage);
        $qty = $item->getQtyOrdered();
        $qty = $this->hlpFormat->toNumber($qty);
        $lots = $this->getOrderLineLots($itemIdMage);
        $tax = $this->getItemTax($item);
        /* PV attributes */
        $pv = $this->dbGetItemPvTotal($itemIdMage);
        $pv = $this->hlpFormat->toNumber($pv);
        /* init Odoo data object */
        $result->setProductIdOdoo($productIdOdoo);
        $result->setQty($qty);
        $result->setLots($lots);
        $result->setTax($tax);
        $result->setPv($pv);
        return $result;
    }

    /**
     * Get lots data from DB and convert into Odoo API data.
     *
     * @param int $itemId
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line\Lot[]
     */
    private function getOrderLineLots($itemId)
    {
        $result = [];
        /* request lots data for the sale item */
        $dbDataLots = $this->dbGetLots($itemId);
        foreach ($dbDataLots as $one) {
            $lot = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line\Lot();
            /* Lot's ID in Odoo */
            $idOdoo = (int)$one->get($this->qbLots::A_ODOO_ID);
            /* skip lot for products w/o lots */
            if ($idOdoo != Cfg::NULL_LOT_ID) $lot->setIdOdoo($idOdoo);
            /* qty in this lot */
            $qty = $one->get($this->qbLots::A_TOTAL);
            $qty = $this->hlpFormat->toNumber($qty);
            $lot->setQty($qty);
            $result[] = $lot;
        }
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line[]
     */
    private function getOrderLines(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $result = [];
        /* collect data */
        $items = $sale->getAllItems();
        foreach ($items as $item) {
            /* process order item */
            $line = $this->getOrderLine($item);
            $result[] = $line;
        }
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Payment[]
     */
    private function getOrderPayments(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $result = [];
        $saleId = $sale->getEntityId();

        /* collect data for main payment */
        $mainPayment = $sale->getPayment();
        $mainCode = $this->manBusinessCodes->getBusCodeForPaymentMethod($mainPayment);
        $ordered = $mainPayment->getBaseAmountOrdered();
        $mainCurr = $sale->getBaseCurrencyCode();
        $mainAmount = $this->hlpFormat->toNumber($ordered);

        /* populate Odoo Data Object */
        $odooPaymentMain = new \Praxigento\Odoo\Repo\Odoo\Data\Payment();
        $odooPaymentMain->setCode($mainCode);
        $odooPaymentMain->setAmount($mainAmount);
        $odooPaymentMain->setCurrency($mainCurr);

        /* validate combo payment (partially usage of wallet) and decrease main payment amount */
        $combo = $this->daoWalletSale->getById($saleId);
        if ($combo) {
            $comboAmount = $combo->getBasePartialAmount();
            $comboAmount = $this->hlpFormat->toNumber($comboAmount);
            $comboCurr = $combo->getBaseCurrency();
            /** @var \Magento\Sales\Model\Order\Payment $comboPayment */
            $comboPayment = $this->factPayment->create();
            $comboPayment->setMethod(\Praxigento\Wallet\Model\Payment\Method\ConfigProvider::CODE_WALLET);
            $comboCode = $this->manBusinessCodes->getBusCodeForPaymentMethod($comboPayment);
            $odooPaymentCombo = new \Praxigento\Odoo\Repo\Odoo\Data\Payment();
            $odooPaymentCombo->setCode($comboCode);
            $odooPaymentCombo->setAmount($comboAmount);
            $odooPaymentCombo->setCurrency($comboCurr);
            $result[] = $odooPaymentCombo;
            /* decrease main payment amount */
            if ($mainCurr != $comboCurr) {
                throw new \Exception("The thing that should not be!! Payment currencies are not equals.");
            }
            $mainAmount = $mainAmount - $comboAmount;
            $mainAmount = $this->hlpFormat->toNumber($mainAmount);
            $odooPaymentMain->setAmount($mainAmount);
        }
        $result[] = $odooPaymentMain;
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price
     */
    private function getOrderPrice(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price();
        /* collect data */
        $currency = $sale->getBaseCurrencyCode();;
        $paid = $sale->getBaseTotalPaid();
        $due = $sale->getBaseTotalDue();
        $paid += $due;
        $paid = $this->hlpFormat->toNumber($paid);
        $tax = $this->getOrderPriceTax($sale);
        /* populate Odoo Data Object */
        $result->setCurrency($currency);
        $result->setPaid($paid);
        $result->setTax($tax);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price\Tax
     */
    private function getOrderPriceTax(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price\Tax();
        /* collect data */
        $total = $sale->getBaseTaxAmount();
        $total = $this->hlpFormat->toNumber($total);
        $base = $sale->getBaseGrandTotal() - $total;
        $base = $this->hlpFormat->toNumber($base);
        /* populate Odoo Data Object */
        $result->setBase($base);
        $result->setTotal($total);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate[]
     */
    private function getOrderPriceTaxRates(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $result = [];
        $saleId = $sale->getId();
        $rows = $this->dbGetOrderTaxes($saleId);
        foreach ($rows as $row) {
            /* collect data */
            $code = $row->get(Cfg::E_SALE_ORDER_TAX_A_CODE);
            $percent = $row->get(Cfg::E_SALE_ORDER_TAX_A_PERCENT);
            $percent = $this->hlpFormat->toNumber($percent, Cfg::ODOO_API_PERCENT_ROUND);
            $amount = $row->get(Cfg::E_SALE_ORDER_TAX_A_AMOUNT);
            $amount = $this->hlpFormat->toNumber($amount);
            /* populate Odoo Data Object */
            $rate = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate();
            $rate->setCode($code);
            $rate->setPercent($percent);
            $rate->setAmount($amount);
            $result[] = $rate;
        }
        return $result;
    }

    /**
     * Convert Magento Order to Odoo API data.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder
     */
    public function getSaleOrder(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder();

        /* Collect order data */
        // id_mage
        $orderIdMage = (int)$sale->getId();
        // warehouse_id_odoo
        $warehouseIdOdoo = $this->getWarehouseIdOdoo($sale);
        // number
        $number = $sale->getIncrementId();
        // date (will be below)
        // customer
        $customer = $this->getOrderCustomer($sale);
        // addr_billing
        $addrBilling = $this->getAddressBilling($sale);
        // addr_shipping
        $addrShipping = $this->getAddressShipping($sale);
        // pv_total (with date paid)
        $pvOrder = $this->daoPvSale->getById($orderIdMage);
        $pvTotal = $this->hlpFormat->toNumber($pvOrder->getTotal());
        $datePaid = $pvOrder->getDatePaid();
        $datePaid = $this->hlpFormat->dateAsRfc3339($datePaid);
        // price
        $price = $this->getOrderPrice($sale);
        // lines
        $lines = $this->getOrderLines($sale);
        // shipping
        $shipping = $this->getShipping($sale);
        // payments
        $payments = $this->getOrderPayments($sale);
        /* populate Odoo Data Object */
        $result->setIdMage($orderIdMage);
        $result->setWarehouseIdOdoo($warehouseIdOdoo);
        $result->setNumber($number);
        $result->setDatePaid($datePaid);
        $result->setCustomer($customer);
        $result->setAddrBilling($addrBilling);
        $result->setAddrShipping($addrShipping);
        $result->setPvTotal($pvTotal);
        $result->setPrice($price);
        $result->setLines($lines);
        $result->setShipping($shipping);
        $result->setPayments($payments);

        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping
     */
    private function getShipping(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping();
        /* collect data */
        $code = $this->manBusinessCodes->getBusCodeForShippingMethod($sale);
        $tax = $this->getShippingTax($sale);
        /* populate Odoo Data Object */
        $result->setCode($code);
        $result->setTax($tax);
        return $result;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping\Tax
     * @throws \Exception
     */
    private function getShippingTax(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping\Tax();
        /* collect data */
        $saleId = $sale->getEntityId();
        $rates = $this->getShippingTaxRates($saleId);
        if ($rates) {
            /* calc base for tax incl. scheme */
            $totalTax = 0;
            foreach ($rates as $rate) {
                $amount = $rate->getAmount();
                $totalTax += $amount;
            }
        } else {
            /* ad fake zero-node to result (SAN-581) */
            $totalTax = 0;
            $rate = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate();
            $rate->setCode('SAN-581');
            $rate->setPercent(0);
            $rate->setAmount(0);
            $rates[] = $rate;
        }
        $shippingWithTax = $sale->getBaseShippingInclTax();
        $base = $shippingWithTax - $totalTax;
        /* populate Odoo Data Object */
        $result->setBase($base);
        $result->setRates($rates);
        return $result;
    }

    /**
     * @param int $saleId
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate[]
     */
    private function getShippingTaxRates($saleId)
    {
        $result = [];
        $rows = $this->dbGetShippingTaxes($saleId);
        foreach ($rows as $row) {
            /* collect data */
            $code = $row->get($this->qbTaxItems::A_TAX_CODE);
            $percent = $row->get($this->qbTaxItems::A_TAX_PERCENT);
            $percent /= 100;
            $percent = $this->hlpFormat->toNumber($percent, Cfg::ODOO_API_PERCENT_ROUND);
            $amount = $row->get($this->qbTaxItems::A_AMOUNT);
            $amount = $this->hlpFormat->toNumber($amount);

            /* populate Odoo Data Object */
            $data = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Tax\Rate();
            $data->setCode($code);
            $data->setPercent($percent);
            $data->setAmount($amount);
            $result[] = $data;
        }
        return $result;
    }

    /**
     * Convert Magento's $storeId to Odoo's $warehouseId.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $sale
     * @return string
     */
    private function getWarehouseIdOdoo(\Magento\Sales\Api\Data\OrderInterface $sale)
    {
        $storeId = $sale->getStoreId();
        $stockId = $this->hlpStock->getStockIdByStoreId($storeId);
        $warehouse = $this->daoWarehouse->getById($stockId);
        $result = $warehouse->getOdooRef();
        return $result;
    }
}