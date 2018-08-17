<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\Shipment;

/**
 * Shipment tracking information.
 */
class TrackingInfo
    extends \Praxigento\Core\Data
{
    /**
     * Business code of the shipping method.
     *
     * @return string
     */
    public function getShippingCode()
    {
        $result = parent::getShippingCode();
        return $result;
    }

    /**
     * Tracking number/code.
     *
     * @return string
     */
    public function getTrackingNumber()
    {
        $result = parent::getTrackingNumber();
        return $result;
    }

    /**
     * Business code of the shipping method.
     *
     * @param string $data
     */
    public function setShippingCode($data)
    {
        parent::setShippingCode($data);
    }

    /**
     * Tracking number/code.
     *
     * @param string $data
     */
    public function setTrackingNumber($data)
    {
        parent::setTrackingNumber($data);
    }
}