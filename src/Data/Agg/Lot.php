<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Agg;

use Flancer32\Lib\DataObject;

/**
 * Aggregate for lot with Odoo related attributes.
 */
class Lot extends DataObject
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_CODE = 'code';
    const AS_EXP_DATE = 'exp_date';
    const AS_ID = 'id';
    const AS_ODOO_ID = 'odoo_id';
    /**#@- */

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