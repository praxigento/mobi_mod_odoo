<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Def;

use Praxigento\Odoo\Repo\IPvModule;
use Praxigento\Pv\Data\Entity\Product as EntityPvProduct;

class PvModule implements IPvModule
{
    /** @var \Praxigento\Core\Repo\IBasic */
    protected $_repoBasic;

    public function __construct(
        \Praxigento\Core\Repo\IBasic $repoBasic
    ) {
        $this->_repoBasic = $repoBasic;
    }

    public function saveProductWholesalePv($mageId, $pv)
    {
        $bind = [
            EntityPvProduct::ATTR_PROD_REF => $mageId,
            EntityPvProduct::ATTR_PV => $pv
        ];
        $this->_repoBasic->addEntity(EntityPvProduct::ENTITY_NAME, $bind);
    }

    public function updateProductWholesalePv($mageId, $pv)
    {
        $bind = [
            EntityPvProduct::ATTR_PROD_REF => $mageId,
            EntityPvProduct::ATTR_PV => $pv
        ];
        $where = EntityPvProduct::ATTR_PROD_REF . '=' . (int)$mageId;
        $this->_repoBasic->updateEntity(EntityPvProduct::ENTITY_NAME, $bind, $where);
    }


}