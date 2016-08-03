<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Def;


class SaleOrderReplicator
    implements \Praxigento\Odoo\Api\SaleOrderReplicatorInterface
{
    /** @var  \Praxigento\Odoo\Service\IReplicate */
    protected $_callReplicate;

    public function __construct(
        \Praxigento\Odoo\Service\IReplicate $callReplicate
    ) {
        $this->_callReplicate = $callReplicate;
    }

    /** @inheritdoc */
    public function shipmentTrackingSave(\Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking $data)
    {
        $req = new \Praxigento\Odoo\Service\Replicate\Request\ShipmentTrackingSave($data->getData());
        $resp = $this->_callReplicate->shipmentTrackingSave($req);
        return $resp;
    }
}