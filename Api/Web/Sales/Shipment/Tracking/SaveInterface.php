<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking;

/**
 * Save shipment data from Odoo to Magento (push replication).
 */
interface SaveInterface
{
    /**
     * Save shipment data from Odoo to Magento (push replication).
     *
     * @param \Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save\Request $request
     * @return \Praxigento\Odoo\Api\Web\Sales\Shipment\Tracking\Save\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}