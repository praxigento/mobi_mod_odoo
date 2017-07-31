<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity;

use Praxigento\Odoo\Data\Entity\Warehouse as Entity;

class Warehouse
    extends \Praxigento\Odoo\Repo\Entity\BaseOdooEntity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /**
     * Get the data instance by ID (ID can be an array for complex primary keys).
     *
     * @param int $id
     * @return \Praxigento\Odoo\Data\Entity\Warehouse|bool Found instance data or 'false'
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

}