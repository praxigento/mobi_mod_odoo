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
    /** @var \Praxigento\Odoo\Repo\Agg\ISaleOrderItem */
    protected $_repoAggSaleOrderItem;

    public function __construct(
        \Praxigento\Warehouse\Tool\IStockManager $manStock,
        \Praxigento\Pv\Repo\Entity\ISale $repoPvSale,
        \Praxigento\Pv\Repo\Entity\Sale\IItem $repoPvSaleItem,
        \Praxigento\Warehouse\Repo\Entity\Quantity\ISale $repoWrhsQtySale,
        \Praxigento\Odoo\Repo\Agg\ISaleOrderItem $repoAggSaleOrderItem
    ) {
        $this->_manStock = $manStock;
        $this->_repoPvSale = $repoPvSale;
        $this->_repoPvSaleItem = $repoPvSaleItem;
        $this->_repoWrhsQtySale = $repoWrhsQtySale;
        $this->_repoAggSaleOrderItem = $repoAggSaleOrderItem;
    }

    public function getOdooLotFormMageQtySale()
    {
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
        $stockId = $this->_manStock->getStockIdByStoreId($storeId);
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
        $aggSaleOrderItems = $this->_repoAggSaleOrderItem->getByOrderAndStock($orderId, $stockId);
        /* collect items data */
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
        $result->setWarehouseId($stockId);

        return $result;
    }
}