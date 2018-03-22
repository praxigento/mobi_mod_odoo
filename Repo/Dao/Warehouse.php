<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Dao;

use Praxigento\Odoo\Repo\Data\Warehouse as Entity;

class Warehouse
    extends \Praxigento\Odoo\Repo\Dao\BaseOdooEntity
{
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\App\Repo\IGeneric $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, Entity::class);
    }

    /**
     * Get the data instance by ID (ID can be an array for complex primary keys).
     *
     * @param int $id
     * @return \Praxigento\Odoo\Repo\Data\Warehouse|bool Found instance data or 'false'
     */
    public function getById($id)
    {
        $result = parent::getById($id);
        return $result;
    }

    public function getByOdooId($id)
    {
        $result = null;
        $conn = $this->getConnection();
        $quoted = $conn->quote($id);
        $where = Entity::ATTR_ODOO_REF . '=' . $quoted;
        $items = $this->repoGeneric->getEntities($this->entityName, null, $where);
        if (
            is_array($items) &&
            (count($items) == 1)
        ) {
            $data = reset($items);
            $result = new $this->entityClassName($data);
        }
        return $result;
    }

}