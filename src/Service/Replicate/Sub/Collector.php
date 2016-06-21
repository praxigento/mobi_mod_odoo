<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub;


class Collector
{
    /** @var  \Praxigento\Pv\Repo\Entity\ISale */
    protected $_repoPvSale;

    public function __construct(
        \Praxigento\Pv\Repo\Entity\ISale $repoPvSale
    ) {
        $this->_repoPvSale = $repoPvSale;
    }

    public function getOdooOrderForMageOrder(\Magento\Sales\Api\Data\OrderInterface $mageOrder)
    {
        $result = new \Praxigento\Odoo\Data\Odoo\SaleOrder();
        $orderId = $mageOrder->getId();
        /* load related data */
        /** @var \Praxigento\Pv\Data\Entity\Sale $pvs */
        $pvs = $this->_repoPvSale->getById($orderId);
        /* Collect order itself data */
        $clientId = $mageOrder->getCustomerId();
        $result->setClientId($clientId);
        $date = $mageOrder->getCreatedAt();
        $result->setDate($date);
        $result->setNumber();
        $result->setPriceDiscountAdditional();
        $result->setPriceTax();
        $result->setShippingMethod();
        $result->setShippingPrice();
        $result->setWarehouseId();
        return $result;
    }
}