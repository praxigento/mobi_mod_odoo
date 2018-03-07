<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Data\SaleOrder\Shipment;


/**
 * Shipment tracking information for sale orders received from Odoo.
 */
class Tracking
    extends \Praxigento\Core\Data
{
    /**
     * Magento ID for sale order.
     *
     * @return int
     */
    public function getSaleOrderIdMage()
    {
        $result = parent::getSaleOrderIdMage();
        return $result;
    }

    /**
     * Shipment data to process.
     *
     * @return \Praxigento\Odoo\Data\Odoo\Shipment
     */
    public function getShipment()
    {
        $result = parent::getShipment();
        return $result;
    }

    /**
     * Magento ID for sale order.
     *
     * @param int $data
     * @return int
     */
    public function setSaleOrderIdMage($data)
    {
        parent::setSaleOrderIdMage($data);
    }

    /**
     * @param \Praxigento\Odoo\Data\Odoo\Shipment $data
     */
    public function setShipment(\Praxigento\Odoo\Data\Odoo\Shipment $data)
    {
        parent::setShipment($data);
    }

}