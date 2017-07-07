<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg\Data;

/**
 * Aggregate for lot with Odoo related attributes.
 */
class Lot
    extends \Flancer32\Lib\Data
{
    /**#@+
     * Aliases for data attributes.
     */
    const AS_CODE = 'code';
    const AS_EXP_DATE = 'exp_date';
    const AS_ID = 'id';
    const AS_ODOO_ID = 'odoo_id';
    /**#@- */

    /** ID & code for the virtual lot that is related to the Odoo products w/o lots */
    const NULL_LOT_CODE = \Praxigento\Odoo\Config::NULL_LOT_CODE;
    const NULL_LOT_ID = \Praxigento\Odoo\Config::NULL_LOT_ID;

    public function getCode()
    {
        $result = parent::get(static::AS_CODE);
        return $result;
    }

    public function getExpDate()
    {
        $result = parent::get(static::AS_EXP_DATE);
        return $result;
    }

    public function getId()
    {
        $result = parent::get(static::AS_ID);
        return $result;
    }

    public function getOdooId()
    {
        $result = parent::get(static::AS_ODOO_ID);
        return $result;
    }

    public function setCode($data)
    {
        parent::set(static::AS_CODE, $data);
    }

    public function setExpDate($data)
    {
        parent::set(static::AS_EXP_DATE, $data);
    }

    public function setId($data)
    {
        parent::set(static::AS_ID, $data);
    }

    public function setOdooId($data)
    {
        parent::set(static::AS_ODOO_ID, $data);
    }

}