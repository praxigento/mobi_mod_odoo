<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account\Saldo\A\Repo\Query;


use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Operation as EOper;
use Praxigento\Accounting\Repo\Data\Transaction as ETran;
use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;
use Praxigento\Accounting\Repo\Data\Type\Operation as ETypeOper;
use Praxigento\Core\App\Repo\Query\Expression as AnExpression;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

/**
 * Base query to construct another query to get summaries for credits & debits for list of customers/operation_types.
 */
class SummaryBase
    extends \Praxigento\Core\App\Repo\Query\Builder
{
    /** Tables aliases for external usage ('camelCase' naming) */
    const AS_CREDIT_ACC = 'crdAcc';
    const AS_CREDIT_CUST = 'crdCust';
    const AS_DEBIT_ACC = 'dbtAcc';
    const AS_DEBIT_CUST = 'dbtCust';
    const AS_OPER = 'opr';
    const AS_TRAN = 'trn';
    const AS_TYPE_ASSET = 'typeAssset';
    const AS_TYPE_OPER = 'typeOpr';

    /** Columns/expressions aliases for external usage ('camelCase' naming) */
    const A_ASSET = 'asset';
    const A_CREDIT_CUST = 'creditCust';
    const A_DEBIT_CUST = 'debitCust';
    const A_SUM = 'sum';

    /** Bound variables names ('camelCase' naming) */
    const BND_ASSET = 'asset';
    const BND_CUSTOMER = 'cust';
    const BND_DATE_FROM = 'dateFrom';
    const BND_DATE_TO = 'dateTo';

    /** Entities are used in the query */
    const E_ACC = EAccount::ENTITY_NAME;
    const E_CUST = EDwnlCust::ENTITY_NAME;
    const E_OPER = EOper::ENTITY_NAME;
    const E_TRAN = ETran::ENTITY_NAME;
    const E_TYPE_ASSET = ETypeAsset::ENTITY_NAME;
    const E_TYPE_OPER = ETypeOper::ENTITY_NAME;

    public function build(\Magento\Framework\DB\Select $source = null)
    {
        /* this is root query builder (started from SELECT) */
        $result = $this->conn->select();

        /* define tables aliases for internal usage (in this method) */
        $asCrdAcc = self::AS_CREDIT_ACC;
        $asCrdCust = self::AS_CREDIT_CUST;
        $asDbtAcc = self::AS_DEBIT_ACC;
        $asDbtCust = self::AS_DEBIT_CUST;
        $asOper = self::AS_OPER;
        $asTrn = self::AS_TRAN;
        $asTypAss = self::AS_TYPE_ASSET;
        $asTypOpr = self::AS_TYPE_OPER;

        /* FROM prxgt_acc_type_operation */
        $tbl = $this->resource->getTableName(self::E_TYPE_OPER);    // name with prefix
        $as = $asTypOpr;    // alias for 'current table' (currently processed in this block of code)
        $cols = [];
        $result->from([$as => $tbl], $cols);    // standard names for the variables

        /* LEFT JOIN prxgt_acc_operation */
        $tbl = $this->resource->getTableName(self::E_OPER);
        $as = $asOper;
        $cols = [];
        $cond = "$as." . EOper::A_TYPE_ID . "=$asTypOpr." . ETypeOper::A_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_transaction */
        $tbl = $this->resource->getTableName(self::E_TRAN);
        $as = $asTrn;
        $exp = $this->expSum();
        $cols = [
            self::A_SUM => $exp
        ];
        $cond = "$as." . ETran::A_OPERATION_ID . "=$asOper." . EOper::A_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_account AS debAcc */
        $tbl = $this->resource->getTableName(self::E_ACC);
        $as = $asDbtAcc;
        $cols = [];
        $cond = "$as." . EAccount::A_ID . "=$asTrn." . ETran::A_DEBIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer AS debCust */
        $tbl = $this->resource->getTableName(self::E_CUST);
        $as = $asDbtCust;
        $cols = [
            self::A_DEBIT_CUST => EDwnlCust::A_MLM_ID
        ];
        $cond = "$as." . EDwnlCust::A_CUSTOMER_ID . "=$asDbtAcc." . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_account AS crdAcc */
        $tbl = $this->resource->getTableName(self::E_ACC);
        $as = $asCrdAcc;
        $cols = [];
        $cond = "$as." . EAccount::A_ID . "=$asTrn." . ETran::A_CREDIT_ACC_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer AS crdCust */
        $tbl = $this->resource->getTableName(self::E_CUST);
        $as = $asCrdCust;
        $cols = [
            self::A_CREDIT_CUST => EDwnlCust::A_MLM_ID
        ];
        $cond = "$as." . EDwnlCust::A_CUSTOMER_ID . "=$asCrdAcc." . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(self::E_TYPE_ASSET);
        $as = $asTypAss;
        $cols = [
            self::A_ASSET => ETypeAsset::A_CODE
        ];
        $cond = "$as." . ETypeAsset::A_ID . "=$asCrdAcc." . EAccount::A_ASSET_TYPE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* query tuning */
        $byFrom = "$asTrn." . ETran::A_DATE_APPLIED . ">=:" . self::BND_DATE_FROM;
        $byTo = "$asTrn." . ETran::A_DATE_APPLIED . "<:" . self::BND_DATE_TO;
        $result->where("($byFrom) AND ($byTo)");

        return $result;
    }

    /**
     * Compose expression to filter result set by customers.
     *
     * @param string[] $mlmIds
     * @param string $as alias for debit/credit customer table
     * @return \Praxigento\Core\App\Repo\Query\Expression
     */
    public function expByCustomers($mlmIds, $as)
    {
        $conn = $this->resource->getConnection();
        $ids = '';
        foreach ($mlmIds as $one) {
            $mlmId = $conn->quote($one);
            $ids .= $mlmId . ',';
        }
        $ids = substr($ids, 0, -1);
        $exp = "$as." . EDwnlCust::A_MLM_ID . " IN ($ids)";
        $result = new AnExpression($exp);
        return $result;
    }

    public function expByOperType($types)
    {
        $conn = $this->resource->getConnection();
        $codes = '';
        foreach ($types as $one) {
            $type = $conn->quote($one);
            $codes .= $type . ',';
        }
        $codes = substr($codes, 0, -1);
        $exp = self::AS_TYPE_ASSET . '.' . ETypeAsset::A_CODE . " IN ($codes)";
        $result = new AnExpression($exp);
        return $result;
    }

    private function expSum()
    {
        $sum = 'SUM(' . self::AS_TRAN . '.' . ETran::A_VALUE . ')';
        $result = new AnExpression($sum);
        return $result;
    }
}