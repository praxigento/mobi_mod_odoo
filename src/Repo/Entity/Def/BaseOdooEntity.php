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
    /** @inheritdoc */
    public function getByOdooId($id)
    {
        $result = null;
        $where = IOdooEntity::ATTR_ODOO_REF . '=' . (int)$id;
        $items = $this->_repoGeneric->getEntities($this->_entityName, null, $where);
        if (
            is_array($items) &&
            (count($items) == 1)
        ) {
            $data = reset($items);
            $result = new $this->_entityClassName($data);
        }
        return $result;
    }

    /** @inheritdoc */
    public function getMageIdByOdooId($id)
    {
        $result = null;
        $item = $this->getByOdooId($id);
        if ($item) {
            $result = $item->getMageRef();
        }
        return $result;
    }

}