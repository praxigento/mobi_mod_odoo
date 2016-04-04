<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Product extends EntityBase
{
    const ENTITY_NAME = 'prxgt_odoo_product';
    const MAGE_REF = 'mage_ref';
    const ODOO_REF = 'odoo_ref';

    /**
     * @inheritdoc
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    /**
     * @return int
     */
    public function getMageRef()
    {
        $result = parent::getData(self::ATTR_MAGE_REF);
        return $result;
    }

    /**
     * @return int
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
        $result = [self::MAGE_REF];
        return $result;
    }

    /**
     * @param int $data
     */
    public function setMageRef($data)
    {
        parent::setData(self::ATTR_MAGE_REF, $data);
    }

    /**
     * @param int $data
     */
    public function setOdooRef($data)
    {
        parent::setData(self::ATTR_ODOO_REF, $data);
    }
}