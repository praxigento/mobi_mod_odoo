<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api;

/**
 * Service to operate with Magento's 'product' entity in MOBI applications.
 * @api
 */
interface ProductInterface {

    /**
     * @param int $id
     *
     * @return null
     */
    public function read($id = null);

}