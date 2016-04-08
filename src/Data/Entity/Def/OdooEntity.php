<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Entity\Def;

use Praxigento\Core\Data\Entity\Base as EntityBase;
use Praxigento\Odoo\Data\Entity\IOdooEntity;

abstract class OdooEntity extends EntityBase implements IOdooEntity
{

    /**
     * @inheritdoc
     */
    public function getMageRef()
    {
        $result = parent::getData(self::ATTR_MAGE_REF);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getOdooRef()
    {
        $result = parent::getData(self::ATTR_ODOO_REF);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        $result = [self::ATTR_MAGE_REF];
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function setMageRef($data)
    {
        parent::setData(self::ATTR_MAGE_REF, $data);
    }

    /**
     * @inheritdoc
     */
    public function setOdooRef($data)
    {
        parent::setData(self::ATTR_ODOO_REF, $data);
    }
}