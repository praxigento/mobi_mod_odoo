<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Odoo\Web\Sales\Shipment\Tracking;

use Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save\Request as ARequest;
use Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save\Request\Data as ARequestData;
use Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save\Response as AResponse;
use Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\SaveInterface as AService;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class SaveTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{
    public function test_exec()
    {
        /** @var AService $obj */
        $obj = $this->manObj->create(AService::class);
        $req = new ARequest();

        $info = new \Praxigento\Odoo\Repo\Odoo\Data\Shipment\TrackingInfo();
        $info->setShippingCode('shipping code');
        $info->setTrackingNumber('track num');

        $ship = new \Praxigento\Odoo\Repo\Odoo\Data\Shipment();
        $ship->setTrackingInfo($info);
        $ship->setIdOdoo(432);

        $data = new ARequestData ();
        $data->setSaleOrderIdMage(436);
        $data->setShipment($ship);
        $req->setData($data);
        $res = $obj->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
    }


}