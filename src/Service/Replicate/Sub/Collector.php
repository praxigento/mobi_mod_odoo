<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub;

class Collector
{
    /** @var \Praxigento\Warehouse\Tool\IStockManager */
    protected $_manStock;
    /** @var \Praxigento\Pv\Repo\Entity\ISale */
    protected $_repoPvSale;
    /** @var \Praxigento\Pv\Repo\Entity\Sale\IItem */
    protected $_repoPvSaleItem;
    /** @var \Praxigento\Warehouse\Repo\Entity\Quantity\ISale */
    protected $_repoWrhsQtySale;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Pv\Repo\Entity\ISale $repoPvSale,
        \Praxigento\Pv\Repo\Entity\Sale\IItem $repoPvSaleItem,
        \Praxigento\Warehouse\Repo\Entity\Quantity\ISale $repoWrhsQtySale
    ) {
        $this->_manStock = $manStock;
        $this->_repoPvSale = $repoPvSale;
        $this->_repoPvSaleItem = $repoPvSaleItem;
        $this->_repoWrhsQtySale = $repoWrhsQtySale;
    }

    public function getOdooLotFormMageQtySale()
    {
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderItemInterface $mageItem
     * @param \Praxigento\Pv\Data\Entity\Sale\Item[] $pvItems
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder\Line
     */
    public function getOdooLineFormMageItem(
        \Magento\Sales\Api\Data\OrderItemInterface $mageItem,
        $pvItems
    ) {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder\Line();
        /* load related data */
        $qtySales = $this->_repoWrhsQtySale->getById($productId);
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

    /**
     * Convert data from Magento format to Odoo API format. Select additional data from DB.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $mageOrder
     * @return \Praxigento\Odoo\Data\Odoo\SaleOrder
     */
    public function getOdooOrderForMageOrder(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder();
        $orderId = $mageOrder->getId();
        $storeId = $mageOrder->getStoreId();
        $wrhsId = $this->_manStock->getStockIdByStoreId($storeId);
        $incId = $mageOrder->getIncrementId();
        $priceDiscountTotal = $mageOrder->getBaseDiscountInvoiced();
        $priceDiscountItems = 0;
        $priceShipping = $mageOrder->getBaseShippingInvoiced(); // TODO: leave one only shipping price
        $priceShipping = $mageOrder->getBaseShippingInclTax();
        $priceTax = $mageOrder->getBaseTaxInvoiced();
        /* load related data */
        /** @var \Praxigento\Pv\Data\Entity\Sale $pvs */
        $pvOrder = $this->_repoPvSale->getById($orderId);
        $datePaid = $pvOrder->getDatePaid();
        $pvItems = $this->_repoPvSaleItem->getItemsByOrderId($orderId);
        /* collect items data */
        $lines = [];
        foreach ($mageOrder->getItems() as $item) {
            $lines[] = $this->getOdooLineFormMageItem($item, $pvItems);
        }
        $result->setLines($lines);
        /* Collect order itself data */
        $clientId = $mageOrder->getCustomerId();
        $result->setClientId($clientId);
        $result->setDate($datePaid);
        $result->setNumber($incId);
        $result->setPriceDiscountAdditional($priceDiscountTotal - $priceDiscountItems);
        $result->setPriceTax($priceTax);
        $result->setShippingMethod();
        $result->setShippingPrice($priceShipping);
        $result->setWarehouseId($wrhsId);

        return $result;
    }
}