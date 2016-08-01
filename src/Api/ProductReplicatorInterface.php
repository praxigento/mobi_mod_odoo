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
     * @param \Praxigento\Odoo\Api\Data\Product\Inventory\Product\Warehouse\Lot $data
     *
     * @return bool
     */
    public function save(\Praxigento\Odoo\Api\Data\Product\Inventory\Product\Warehouse\Lot $data);
}