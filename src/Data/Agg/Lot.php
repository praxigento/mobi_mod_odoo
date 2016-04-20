<?php
/**
 * Aggregation for
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Agg;

use Flancer32\Lib\DataObject;

class Lot extends DataObject
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_CODE = 'Code';
    const AS_EXP_DATE = 'ExpDate';
    const AS_ID = 'Id';
    const AS_ODOO_ID = 'OdooId';

    /**#@-*/

    public function getCode()
    {
        $result = parent::getData(static::AS_CODE);
        return $result;
    }

    public function getExpDate()
    {
        $result = parent::getData(static::AS_EXP_DATE);
        return $result;
    }

    public function getId()
    {
        $result = parent::getData(static::AS_ID);
        return $result;
    }

    public function getOdooId()
    {
        $result = parent::getData(static::AS_ODOO_ID);
        return $result;
    }

    public function setCode($data)
    {
        parent::setData(static::AS_CODE, $data);
    }

    public function setExpDate($data)
    {
        parent::setData(static::AS_EXP_DATE, $data);
    }

    public function setId($data)
    {
        parent::setData(static::AS_ID, $data);
    }

    public function setOdooId($data)
    {
        parent::setData(static::AS_ODOO_ID, $data);
    }

}