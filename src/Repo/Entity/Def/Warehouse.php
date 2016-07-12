<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Entity\Def;

use Praxigento\Odoo\Data\Entity\Warehouse as Entity;

class Warehouse
    extends \Praxigento\Odoo\Repo\Entity\Def\BaseOdooEntity
    implements \Praxigento\Odoo\Repo\Entity\IWarehouse
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

}