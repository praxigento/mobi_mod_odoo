<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Dao;

use Praxigento\Odoo\Repo\Data\Lot as Entity;

class Lot
    extends \Praxigento\Odoo\Repo\Dao\BaseOdooEntity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

}