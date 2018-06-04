<?php

namespace Praxigento\Odoo\Ui\DataProvider\Grid\Inventory;

use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Repo\Data\Product as EProduct;

class QueryBuilder
    extends \Praxigento\Warehouse\Ui\DataProvider\Grid\Inventory\QueryBuilder
{
    /**#@+ Tables aliases for external usage ('camelCase' naming) */
    const AS_ODOO = 'pow';
    /**#@- */

    /**#@+
     * Aliases for data attributes.
     */
    const A_ODOO_ID = 'OdooId';

    /**#@- */


    protected function getMapper()
    {
        if (is_null($this->mapper)) {
            /* init parent mapper */
            $this->mapper = parent::getMapper();
            /* then add own aliases */
            $key = self::A_ODOO_ID;
            $value = self::AS_ODOO . '.' . EProduct::A_ODOO_REF;
            $this->mapper->add($key, $value);
        }
        $result = $this->mapper;
        return $result;
    }

    protected function getQueryItems()
    {
        $result = parent::getQueryItems();

        /* aliases and tables */
        $asProd = parent::AS_CATALOG_PRODUCT_ENTITY;
        $asOdoo = self::AS_ODOO;

        $tbl = $this->resource->getTableName(EProduct::ENTITY_NAME);
        $as = $asOdoo;
        /* LEFT JOIN prxgt_odoo_prod */
        $cols = [
            self::A_ODOO_ID => EProduct::A_ODOO_REF
        ];
        $cond = $asOdoo . '.' . EProduct::A_MAGE_REF . '=' . $asProd . '.' . Cfg::E_PRODUCT_A_ENTITY_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

}