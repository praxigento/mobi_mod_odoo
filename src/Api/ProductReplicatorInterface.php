<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api;

/**
 * Service to replicate product data between Magento 2 & Odoo.
 * @api
 */
interface ProductReplicatorInterface
{

    /**
     * @param \Praxigento\Odoo\Api\Data\IBundle $data
     *
     * @return null
     */
    public function save(\Praxigento\Odoo\Api\Data\IBundle $data);

}