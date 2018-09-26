<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Odoo\Repo\Odoo\Data\SaleOrder;

use Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Response as AnObject;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class ResponseTest
    extends \Praxigento\Core\Test\BaseCase\Unit
{

    private function getInvoices()
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Invoice();
        $result->setIdOdoo(43);
        $result->setStatus('status');
        return [$result];
    }

    private function getShipmentInfo()
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Shipment\TrackingInfo();
        $result->setTrackingNumber('number');
        $result->setShippingCode('code');
        return $result;
    }

    private function getShipments()
    {
        $info = $this->getShipmentInfo();
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Shipment();
        $result->setIdOdoo(32);
        $result->setStatus('status');
        $result->setTrackingInfo($info);
        return [$result];
    }

    public function test_convert()
    {
        /* create object & convert it to 'JSON'-array */
        $obj = new AnObject();

        $invoices = $this->getInvoices();
        $shipments = $this->getShipments();

        $obj->setIdMage(21);
        $obj->setIdOdoo(12);
        $obj->setInvoices($invoices);
        $obj->setShipments($shipments);
        $obj->setStatus('status');

        /** @var \Magento\Framework\Webapi\ServiceOutputProcessor $output */
        $output = $this->manObj->get(\Magento\Framework\Webapi\ServiceOutputProcessor::class);
        $json = $output->convertValue($obj, AnObject::class);

        /* convert 'JSON'-array to object */
        /** @var \Magento\Framework\Webapi\ServiceInputProcessor $input */
        $input = $this->manObj->get(\Magento\Framework\Webapi\ServiceInputProcessor::class);
        $data = $input->convertValue($json, AnObject::class);
        $this->assertNotNull($data);
    }
}