<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Odoo;

/**
 * Shipment data corresponded to sale order.
 */
class Shipment
    extends \Praxigento\Core\Data
{
    /**
     * ID of the shipment in Odoo.
     *
     * @return int
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
        return $result;
    }

    /**
     * Status of the shipment in Odoo (deprecated).
     *
     * @return string
     *
     * @deprecated is not used in \Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save::execute
     */
    public function getStatus()
    {
        $result = parent::getStatus();
        return $result;
    }

    /**
     * Tracking information from Odoo.
     *
     * @return \Praxigento\Odoo\Data\Odoo\Shipment\TrackingInfo|null
     */
    public function getTrackingInfo()
    {
        $result = parent::getTrackingInfo();
        return $result;
    }

    /**
     * ID of the shipment in Odoo.
     *
     * @param int $data
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }

    /**
     * Status of the shipment in Odoo.
     *
     * @param string $data
     *
     * @deprecated is not used in \Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save::execute
     */
    public function setStatus($data)
    {
        parent::setStatus($data);
    }

    /**
     * Tracking information from Odoo.
     *
     * @param \Praxigento\Odoo\Data\Odoo\Shipment\TrackingInfo $data
     */
    public function setTrackingInfo(\Praxigento\Odoo\Data\Odoo\Shipment\TrackingInfo $data)
    {
        parent::setTrackingInfo($data);
    }
}