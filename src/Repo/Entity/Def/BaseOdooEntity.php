<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity\Def;

use Praxigento\Odoo\Data\Entity\IOdooEntity;

/**
 * Base class for repo to operate with entities in Odoo registries.
 */
abstract class BaseOdooEntity
    extends \Praxigento\Core\Repo\Def\Entity
    implements \Praxigento\Odoo\Repo\Entity\IOdooEntity
{
    public function getByOdooId($id)
    {
        $result = null;
        $where = IOdooEntity::ATTR_ODOO_REF . '=' . (int)$id;
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
        /** @var \Praxigento\Odoo\Data\Entity\IOdooEntity $item */
        $item = $this->getById($id);
        if ($item) {
            $result = $item->getOdooRef();
        }
        return $result;
    }

}