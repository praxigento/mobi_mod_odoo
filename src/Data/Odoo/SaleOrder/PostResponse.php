<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Odoo\SaleOrder;

use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Data\Odoo\Invoice;
use Praxigento\Odoo\Data\Odoo\Shipment;

/**
 * Response for "/sale_order" POST operation.
 */
class PostResponse extends DataObject
{
    /**
     * @return int
     */
    public function getId()
    {
        $result = parent::getId();
        return $result;
    }

    /**
     * @param int $data
     */
    public function setId($data = null)
    {
        parent::setId($data);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $result = parent::getStatus();
        return $result;
    }

    /**
     * @param string $data
     */
    public function setStatus($data = null)
    {
        parent::setStatus($data);
    }

    /**
     * @return Invoice[]
     */
    public function getInvoices()
    {
        $result = parent::getInvoices();
        return $result;
    }

    /**
     * @param Invoice[] $data
     */
    public function setInvoices($data = null)
    {
        parent::setInvoices($data);
    }

    /**
     * @return Shipment[]
     */
    public function getShipments()
    {
        $result = parent::getShipments();
        return $result;
    }

    /**
     * @param Shipment[] $data
     */
    public function setShipments($data = null)
    {
        parent::setShipments($data);
    }
}