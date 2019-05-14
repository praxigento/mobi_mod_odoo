<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Odoo\Web\Customer\Get\A\Repo\Query;

use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;
use Praxigento\Odoo\Config as Cfg;

class GetAll
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_CUST = 'cust';
    const AS_DWNL = 'dwnl';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_ID_GROUP = 'idGroup';
    const A_ID_MAGE = 'idMage';
    const A_ID_MLM = 'idMlm';
    const A_NAME = 'name';

    /** Entities are used in the query */
    const E_CUST = Cfg::ENTITY_MAGE_CUSTOMER;
    const E_DWNL = EDwnlCust::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asCust = self::AS_CUST;
        $asDwnl = self::AS_DWNL;

        /* FROM customer_entity */
        $tbl = $this->resource->getTableName(self::E_CUST);
        $as = $asCust;
        $expName = $this->getExpForCustName();
        $cols = [
            self::A_ID_MAGE => Cfg::E_CUSTOMER_A_ENTITY_ID,
            self::A_ID_GROUP => Cfg::E_CUSTOMER_A_GROUP_ID,
            self::A_NAME => $expName
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_dwnl_customer */
        $tbl = $this->resource->getTableName(self::E_DWNL);
        $as = $asDwnl;
        $cols = [
            self::A_ID_MLM => EDwnlCust::A_MLM_ID
        ];
        $cond = "$as." . EDwnlCust::A_CUSTOMER_REF . "=$asCust." . Cfg::E_CUSTOMER_A_ENTITY_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        return $result;

    }

    public function getExpForCustName()
    {
        $value = 'CONCAT(' . self::AS_CUST . '.' . Cfg::E_CUSTOMER_A_FIRSTNAME . ", ' ', " .
            self::AS_CUST . '.' . Cfg::E_CUSTOMER_A_LASTNAME . ')';
        $result = new \Praxigento\Core\App\Repo\Query\Expression($value);
        return $result;
    }
}