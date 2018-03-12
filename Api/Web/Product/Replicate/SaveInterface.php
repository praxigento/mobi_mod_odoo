<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Product\Replicate;

/**
 * Save product inventory data to Magento (push replication).
 */
interface SaveInterface
{
    /**
     * Command to save product inventory data to Magento (push replication).
     *
     * @param \Praxigento\Odoo\Api\Web\Product\Replicate\Save\Request $request
     * @return \Praxigento\Odoo\Api\Web\Product\Replicate\Save\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}