<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Lib\Data\Entity;

use Praxigento\Core\Entity\Base as EntityBase;

class Warehouse extends EntityBase
{
    const ATTR_CURRENCY = 'currency';
    const ATTR_MAGE_ID = 'mage_ref';
    const ATTR_ODOO_ID = 'odoo_ref';
    const ENTITY_NAME = 'prxgt_odoo_wrhs';

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
        $result = [self::ATTR_MAGE_ID];
        return $result;
    }
}