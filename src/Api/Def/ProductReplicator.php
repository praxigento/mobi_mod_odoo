<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Def;

use Praxigento\Odoo\Api\Data;
use Praxigento\Odoo\Api\ProductReplicatorInterface;

class ProductReplicator implements ProductReplicatorInterface
{
    public function save(Data\Product\Replicator\IBundle $data)
    {
        return;
    }

}