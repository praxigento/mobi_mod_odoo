<?php

namespace Praxigento\Odoo\Ui\DataProvider\Grid\Warehouse;

use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Repo\Entity\Data\Warehouse as EWarehouse;

class QueryBuilder
    extends \Praxigento\Warehouse\Ui\DataProvider\Grid\Warehouse\QueryBuilder
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
            $value = self::AS_ODOO . '.' . EWarehouse::ATTR_ODOO_REF;
            $this->mapper->add($key, $value);
        }
        $result = $this->mapper;
        return $result;
    }

    protected function getQueryItems()
    {
        $result = parent::getQueryItems();

        /* aliases and tables */
        $asStock = parent::AS_STOCK;
        $asOdoo = self::AS_ODOO;

        $tbl = $this->resource->getTableName(EWarehouse::ENTITY_NAME);
        $as = $asOdoo;
        /* LEFT JOIN prxgt_odoo_wrhs */
        $cols = [
            self::A_ODOO_ID => EWarehouse::ATTR_ODOO_REF
        ];
        $cond = $asOdoo . '.' . EWarehouse::ATTR_MAGE_REF . '=' . $asStock . '.' . Cfg::E_CATINV_STOCK_A_STOCK_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;
    }

}