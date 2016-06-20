<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Odoo;

use Flancer32\Lib\DataObject;

class Shipment extends DataObject
{

    public function getId()
    {
        $result = parent::getId();
        return $result;
    }

    public function getStatus()
    {
        $result = parent::getStatus();
        return $result;
    }

    public function getTrackingNumber()
    {
        $result = parent::getTrackingNumber();
        return $result;
    }

    public function getTrackingType()
    {
        $result = parent::getTrackingType();
        return $result;
    }

    public function getTrackingUrl()
    {
        $result = parent::getTrackingUrl();
        return $result;
    }

    public function setId($data = null)
    {
        parent::setId($data);
    }

    public function setStatus($data = null)
    {
        parent::setStatus($data);
    }

    public function setTrackingNumber($data = null)
    {
        parent::setTrackingNumber($data);
    }

    public function setTrackingType($data = null)
    {
        parent::setTrackingType($data);
    }

    public function setTrackingUrl($data = null)
    {
        parent::setTrackingUrl($data);
    }

}