<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Account\Daily\Own\Repo\Query;

use Praxigento\Accounting\Repo\Data\Operation as EOper;
use Praxigento\Accounting\Repo\Data\Transaction as ETrans;

/**
 * Get summary for transactions by operation type
 */
class GetTransSummary
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_OPER = 'oper';
    const AS_TRANS = 'trans';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_OPER_TYPE = 'operType';
    const A_VALUE = 'value';

    /** Bound variables names ('camelCase' naming) */
    const BND_DATE_FROM = 'dateFrom';
    const BND_DATE_TO = 'dateTo';

    /** Entities are used in the query */
    const E_OPER = EOper::ENTITY_NAME;
    const E_TRANS = ETrans::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        $result = $this->conn->select();
        /* define tables aliases for internal usage (in this method) */
        $asOper = self::AS_OPER;
        $asTrans = self::AS_TRANS;

        /* FROM prxgt_acc_transaction */
        $tbl = $this->resource->getTableName(self::E_TRANS);
        $as = $asTrans;
        $exp = "SUM($asTrans." . ETrans::A_VALUE . ")";
        $exp = new \Praxigento\Core\App\Repo\Query\Expression($exp);
        $cols = [
            self::A_VALUE => $exp
        ];
        $result->from([$as => $tbl], $cols);

        /* LEFT JOIN prxgt_acc_operation to get operation type */
        $tbl = $this->resource->getTableName(self::E_OPER);
        $as = $asOper;
        $cols = [
            self::A_OPER_TYPE => EOper::A_TYPE_ID
        ];
        $cond = $as . '.' . EOper::A_ID . '=' . $asTrans . '.' . ETrans::A_OPERATION_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* WHERE */
        $byFrom = "$asTrans." . ETrans::A_DATE_APPLIED . '>=:' . self::BND_DATE_FROM;
        $byTo = "$asTrans." . ETrans::A_DATE_APPLIED . '<:' . self::BND_DATE_TO;
        $result->where("($byFrom) AND ($byTo)");

        /* GROUP */
        $result->group($asOper . '.' . EOper::A_TYPE_ID);

        return $result;
    }
}