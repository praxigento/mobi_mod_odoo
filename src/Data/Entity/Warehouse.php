<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Warehouse extends EntityBase
{
    const ATTR_CURRENCY = 'currency';
    const ATTR_MAGE_ID = 'mage_ref';
    const ATTR_ODOO_ID = 'odoo_ref';
    const ENTITY_NAME = 'prxgt_odoo_wrhs';

    /**
     * @return string
     */
    public function getCurrency()
    {
        $result = parent::getData(self::ATTR_CURRENCY);
        return $result;
    }

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
        $result = [self::ATTR_MAGE_ID];
        return $result;
    }

    /**
     * @param string $data
     */
    public function setCurrency($data)
    {
        parent::setData(self::ATTR_CURRENCY, $data);
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