<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api;

/**
 * Service to push product data from Odoo to Magento.
 *
 * @api
 */
interface ProductReplicatorInterface
{
    /**
     * Save product inventory data to Magento (push replication).
     *
     * @param \Praxigento\Odoo\Data\Odoo\Inventory $data
     *
     * @return bool
     */
    public function save(\Praxigento\Odoo\Data\Odoo\Inventory $data);
}