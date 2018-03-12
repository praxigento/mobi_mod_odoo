<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Product\Replicate\Save\Request;

/**
 * Save product inventory data to Magento (push replication).
 * This data object is the same as Odoo Inventory data object.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Data
    extends \Praxigento\Odoo\Data\Odoo\Inventory
{
}