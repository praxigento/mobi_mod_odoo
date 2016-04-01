<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Lib\Data\Entity;

use Praxigento\Core\Entity\Base as EntityBase;

class Lot extends EntityBase
{
    const ATTR_MAGE_REF = 'mage_ref';
    const ATTR_ODOO_REF = 'odoo_ref';
    const ENTITY_NAME = 'prxgt_odoo_lot';

    /**
     * @inheritdoc
     */
    public function getEntityName()
    {
        return self::ENTITY_NAME;
    }

    /**
     * @inheritdoc
     */
    public function getPrimaryKeyAttrs()
    {
        $result = [self::ATTR_MAGE_REF];
        return $result;
    }
}