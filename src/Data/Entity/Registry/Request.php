<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Entity\Registry;

/**
 * Odoo requests registry to prevent double processing.
 */
class Request
    extends \Praxigento\Core\Data\Entity\Base
{
    const ATTR_ODOO_REF = 'odoo_ref';
    const ATTR_TYPE_CODE = 'type_code';
    const ENTITY_NAME = 'prxgt_odoo_reg_request';

    public function getPrimaryKeyAttrs()
    {
        return [self::ATTR_TYPE_CODE, self::ATTR_ODOO_REF];
    }
}