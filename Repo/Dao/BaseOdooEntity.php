<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Dao;

use Praxigento\Odoo\Repo\Data\IOdooEntity;

/**
 * Base class for repo to operate with entities in Odoo registries.
 */
abstract class BaseOdooEntity
    extends \Praxigento\Core\App\Repo\Dao
    implements \Praxigento\Odoo\Repo\Dao\IOdooDao
{
    public function getByOdooId($id)
    {
        $result = null;
        $where = IOdooEntity::A_ODOO_REF . '=' . (int)$id;
        $items = $this->daoGeneric->getEntities($this->entityName, null, $where);
        if (
            is_array($items) &&
            (count($items) == 1)
        ) {
            $data = reset($items);
            $result = new $this->entityClassName($data);
        }
        return $result;
    }

    public function getMageIdByOdooId($id)
    {
        $result = null;
        $item = $this->getByOdooId($id);
        if ($item) {
            $result = $item->getMageRef();
        }
        return $result;
    }

    public function getOdooIdByMageId($id)
    {
        $result = null;
        /** @var \Praxigento\Odoo\Repo\Data\IOdooEntity $item */
        $item = $this->getById($id);
        if ($item) {
            $result = $item->getOdooRef();
        }
        return $result;
    }

}