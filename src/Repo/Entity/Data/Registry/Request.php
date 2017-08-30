<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity\Data\Registry;

/**
 * Odoo requests registry to prevent double processing.
 */
class Request
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_ODOO_REF = 'odoo_ref';
    const ATTR_TYPE_CODE = 'type_code';
    const ENTITY_NAME = 'prxgt_odoo_reg_request';

    /**
     * @return string
     */
    public function getOdooRef()
    {
        $result = parent::get(self::ATTR_ODOO_REF);
        return $result;
    }

    public static function getPrimaryKeyAttrs()
    {
        return [self::ATTR_TYPE_CODE, self::ATTR_ODOO_REF];
    }

    /**
     * @return integer
     */
    public function getTypeCode()
    {
        $result = parent::get(self::ATTR_TYPE_CODE);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setOdooRef($data)
    {
        parent::set(self::ATTR_ODOO_REF, $data);
    }

    /**
     * @param integer $data
     */
    public function setTypeCode($data)
    {
        parent::set(self::ATTR_TYPE_CODE, $data);
    }
}