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
     * @param \Praxigento\Odoo\Lib\Data\Dict\IBundle $data
     *
     * @return null
     */
    public function save(\Praxigento\Odoo\Lib\Data\Dict\IBundle $data);

}