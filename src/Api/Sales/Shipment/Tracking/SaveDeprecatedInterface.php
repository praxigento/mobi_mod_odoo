<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Sales\Shipment\Tracking;

/**
 * Don't use this service (deprecated). Use "praxigentoOdooSalesShipmentTrackingSaveV1" instead.
 *
 * @api
 * @deprecated
 */
interface SaveDeprecatedInterface
{
    /**
     * Save shipment data to Magento (push replication).
     *
     * @param \Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking $data
     *
     * @return boolean
     */
    public function execute(\Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking $data);
}