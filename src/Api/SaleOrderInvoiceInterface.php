<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api;

/**
 * Service to operate with Magento's 'sale order invoice' entity in MOBI applications.
 * @api
 */
interface SaleOrderInvoiceInterface {

    /**
     * @param int $id
     *
     * @return null
     */
    public function read($id = null);

}