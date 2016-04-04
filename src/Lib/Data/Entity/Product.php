<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Lib\Data\Entity;

use Praxigento\Core\Data\Entity\Base as EntityBase;

class Product extends EntityBase
{
    const ENTITY_NAME = 'prxgt_odoo_product';
    const MAGE_ID = 'mage_id';
    const ODOO_ID = 'odoo_id';


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
        $result = [self::MAGE_ID];
        return $result;
    }
}