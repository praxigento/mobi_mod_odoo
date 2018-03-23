<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Data\Def;

abstract class OdooEntity
    extends \Praxigento\Core\App\Repo\Data\Entity\Base
    implements \Praxigento\Odoo\Repo\Data\IOdooEntity
{

    public function getDateReplicated()
    {
        $result = parent::get(static::A_DATE_REPLICATED);
        return $result;
    }

    public function getMageRef()
    {
        $result = parent::get(static::A_MAGE_REF);
        return $result;
    }

    public function getOdooRef()
    {
        $result = parent::get(static::A_ODOO_REF);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        $result = [static::A_MAGE_REF];
        return $result;
    }

    public function setDateReplicated($data)
    {
        parent::set(static::A_DATE_REPLICATED, $data);
    }

    public function setMageRef($data)
    {
        parent::set(static::A_MAGE_REF, $data);
    }

    public function setOdooRef($data)
    {
        parent::set(static::A_ODOO_REF, $data);
    }
}