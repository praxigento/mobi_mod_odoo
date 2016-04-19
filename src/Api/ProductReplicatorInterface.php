<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api;

use Praxigento\Odoo\Data\Api\IBundle;

/**
 * Service to replicate product data between Magento 2 & Odoo.
 * @api
 */
interface ProductReplicatorInterface
{

    /**
     * @param \Praxigento\Odoo\Data\Api\IBundle $data
     *
     * @return null
     */
    public function save(IBundle $data);

}