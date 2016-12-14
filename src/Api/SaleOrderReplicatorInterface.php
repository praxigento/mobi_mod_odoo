<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api;

/**
 * Service to push sale order data from Odoo to Magento.
 *
 * @api
 */
interface SaleOrderReplicatorInterface
{
    /**
     * Save shipment data to Magento (push replication).
     *
     * @param \Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking $data
     *
     * @return boolean
     */
    public function shipmentTrackingSave(\Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking $data);
}