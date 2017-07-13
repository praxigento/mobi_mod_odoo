<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Entity\Def;

abstract class OdooEntity
    extends \Praxigento\Core\Data\Entity\Base
    implements \Praxigento\Odoo\Data\Entity\IOdooEntity
{

    /** @inheritdoc */
    public function getDateReplicated()
    {
        $result = parent::get(static::ATTR_DATE_REPLICATED);
        return $result;
    }

    /** @inheritdoc */
    public function getMageRef()
    {
        $result = parent::get(static::ATTR_MAGE_REF);
        return $result;
    }

    /** @inheritdoc */
    public function getOdooRef()
    {
        $result = parent::get(static::ATTR_ODOO_REF);
        return $result;
    }

    /** @inheritdoc */
    public static function getPrimaryKeyAttrs()
    {
        $result = [static::ATTR_MAGE_REF];
        return $result;
    }

    /** @inheritdoc */
    public function setDateReplicated($data)
    {
        parent::set(static::ATTR_DATE_REPLICATED, $data);
    }

    /** @inheritdoc */
    public function setMageRef($data)
    {
        parent::set(static::ATTR_MAGE_REF, $data);
    }

    /** @inheritdoc */
    public function setOdooRef($data)
    {
        parent::set(static::ATTR_ODOO_REF, $data);
    }
}