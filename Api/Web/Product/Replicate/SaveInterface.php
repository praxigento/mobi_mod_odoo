<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Product\Replicate;

/**
 * Save product inventory data to Magento (push replication).
 *
 * @api
 */
interface SaveInterface
{
    /**
     * Command to save product inventory data to Magento (push replication).
     *
     * @param \Praxigento\Odoo\Data\Odoo\Inventory $data
     * @return bool
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($data);
}