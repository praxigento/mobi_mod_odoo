<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking;

/**
 * Save shipment data from Odoo to Magento (push replication).
 *
 * @api
 */
interface SaveInterface
{
    /**
     * Save shipment data from Odoo to Magento (push replication).
     *
     * @param \Praxigento\Odoo\Api\Data\SaleOrder\Shipment\Tracking $data
     * @return boolean
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($data);
}