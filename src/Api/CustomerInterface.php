<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api;

/**
 * Service to operate with Magento's 'customer' entity in MOBI applications.
 * @api
 */
interface CustomerInterface {

    /**
     * @param int $id
     *
     * @return null
     */
    public function read($id = null);

}