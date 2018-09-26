<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder;

/**
 * Response for "/sale_order" POST operation.
 */
class Response
    extends \Praxigento\Core\Data
{
    /**
     * @return int
     */
    public function getIdMage()
    {
        $result = parent::getIdMage();
        return $result;
    }

    /**
     * @return int
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
        return $result;
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
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Invoice[]
     */
    public function getInvoices()
    {
        $result = parent::getInvoices();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Shipment[]
     */
    public function getShipments()
    {
        $result = parent::getShipments();
        return $result;
    }

    /**
     * @param int $data
     * @return void
     */
    public function setIdMage($data)
    {
        parent::setIdMage($data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setStatus($data)
    {
        parent::setStatus($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Invoice[] $data
     * @return void
     */
    public function setInvoices($data)
    {
        parent::setInvoices($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Shipment[] $data
     * @return void
     */
    public function setShipments($data)
    {
        parent::setShipments($data);
    }
}