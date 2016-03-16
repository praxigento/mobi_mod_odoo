<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api;

/**
 * Service to operate with Magento's 'sale order shipment' entity in MOBI applications.
 * @api
 */
interface SaleOrderShipmentInterface {

    /**
     * @param int $id
     *
     * @return null
     */
    public function read($id = null);

}