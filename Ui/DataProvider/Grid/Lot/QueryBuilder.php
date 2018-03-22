<?php

namespace Praxigento\Odoo\Ui\DataProvider\Grid\Lot;

use Praxigento\Odoo\Repo\Data\Lot as ELot;
use Praxigento\Warehouse\Repo\Data\Lot as EWrhsLot;

class QueryBuilder
    extends \Praxigento\Warehouse\Ui\DataProvider\Grid\Lot\QueryBuilder
{
    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_ODOO = 'pol';
    const AS_WRHS = 'pwl';
    /**#@- */

    /**#@+
     * Aliases for data attributes.
     */
    const A_ODOO_ID = 'odooId';

    /**#@- */

    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            /* init parent mapper */
            $this->mapper = parent::getMapper();
            /* then add own aliases */
            $key = self::A_ODOO_ID;
            $value = self::AS_ODOO . '.' . ELot::ATTR_ODOO_REF;
            $this->mapper->add($key, $value);
        }
        $result = $this->mapper;
        return $result;
    }

    protected function getQueryItems()
    {
        $result = parent::getQueryItems();

        $asOdoo = self::AS_ODOO;
        $asLot = parent::AS_LOT;

        /* LEFT JOIN prxgt_odoo_lot */
        $tbl = $this->resource->getTableName(ELot::ENTITY_NAME);
        $as = $asOdoo;
        $cols = [
            self::A_ODOO_ID => ELot::ATTR_ODOO_REF
        ];
        $cond = $asOdoo . '.' . ELot::ATTR_MAGE_REF . '=' . $asLot . '.' . EWrhsLot::ATTR_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }
}
