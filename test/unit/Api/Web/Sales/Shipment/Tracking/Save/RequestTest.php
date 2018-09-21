<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save;

use Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save\Request as AnObject;

include_once(__DIR__ . '/../../../../../../phpunit_bootstrap.php');

class RequestTest
    extends \Praxigento\Core\Test\BaseCase\Unit
{
    private function getDataShipment()
    {
        $info = $this->getDataShipmentInfo();

        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Shipment();
        $result->setIdOdoo(43);
        $result->setTrackingInfo($info);
        $result->setStatus('status');
        return $result;
    }

    private function getDataShipmentInfo()
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Shipment\TrackingInfo();
        $result->setShippingCode('code');
        $result->setTrackingNumber('number');
        return $result;
    }

    public function test_convert()
    {
        /* create object & convert it to 'JSON'-array */
        $obj = new AnObject();

        $shipment = $this->getDataShipment();

        $data = new \Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save\Request\Data();
        $data->setSaleOrderIdMage(32);
        $data->setShipment($shipment);
        $obj->setData($data);

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