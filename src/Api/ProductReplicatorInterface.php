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
     * @param \Praxigento\Odoo\Api\Data\Product\Replicator\IBundle $data
     *
     * @return null
     */
    public function save(Data\Product\Replicator\IBundle $data);

}