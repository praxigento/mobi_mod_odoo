<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account\Transaction\A;

use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Transaction as ETransaction;
use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;
use Praxigento\Downline\Repo\Query\Account\Trans\Get as QTransGet;
use Praxigento\Odoo\Api\Web\Account\Transaction\Response\Data\Item as DItem;
use Praxigento\Odoo\Config as Cfg;

/**
 * Retrieve transactions data from DB amd compose API data object.
 */
class GetItems
{
    /** Tables aliases for internal usage ('camelCase' naming) */
    private const AS_CUST_CREDIT = 'custCred';
    private const AS_CUST_DEBIT = 'custDebt';

    /** Columns/expressions aliases for internal usage ('camelCase' naming) */
    const A_CRED_CUST_FIRST = 'credCustFirst';
    const A_CRED_CUST_LAST = 'credCustLast';
    const A_DEBT_CUST_FIRST = 'debtCustFirst';
    const A_DEBT_CUST_LAST = 'debtCustLast';

    /** Bound variables names ('camelCase' naming) */
    private const BND_ASSET_CODE = 'assetCode';
    private const BND_DATE_FROM = 'dateFrom';
    private const BND_DATE_TO = 'dateTo';
    private const BND_MLM_ID = 'mlmId';

    /** @var \Praxigento\Downline\Repo\Query\Account\Trans\Get */
    private $qTransGet;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Downline\Repo\Query\Account\Trans\Get $qTransGet
    ) {
        $this->resource = $resource;
        $this->qTransGet = $qTransGet;
    }

    public function exec($assetTypeCode, $mlmId, $dateFrom, $dateTo)
    {
        /** define local working data */
        $query = $this->qTransGet->build();
        $conn = $query->getConnection();
        $dateFrom = substr($dateFrom, 0, 10); // YYYY-MM-DD
        $dateTo = substr($dateTo, 0, 10); // YYYY-MM-DD
        $dateToNext = date('Y-m-d', strtotime($dateTo . ' +1 day'));

        /* perform processing */
        /* define tables aliases for internal usage (in this method) */
        $asAccCred = QTransGet::AS_ACC_CREDIT;
        $asAccDebt = QTransGet::AS_ACC_DEBIT;
        $asCustCred = self::AS_CUST_CREDIT;
        $asCustDebt = self::AS_CUST_DEBIT;

        /* additional JOINS to base query */

        /* LEFT JOIN customer_entity as debit */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $as = $asCustDebt;
        $cols = [
            self::A_DEBT_CUST_FIRST => Cfg::E_CUSTOMER_A_FIRSTNAME,
            self::A_DEBT_CUST_LAST => Cfg::E_CUSTOMER_A_LASTNAME
        ];
        $cond = "$as." . Cfg::E_CUSTOMER_A_ENTITY_ID . "=$asAccDebt." . EAccount::A_CUST_ID;
        $query->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN customer_entity as credit */
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $as = $asCustCred;
        $cols = [
            self::A_CRED_CUST_FIRST => Cfg::E_CUSTOMER_A_FIRSTNAME,
            self::A_CRED_CUST_LAST => Cfg::E_CUSTOMER_A_LASTNAME
        ];
        $cond = "$as." . Cfg::E_CUSTOMER_A_ENTITY_ID . "=$asAccCred." . EAccount::A_CUST_ID;
        $query->joinLeft([$as => $tbl], $cond, $cols);

        /** add filters to query */
        if (is_null($mlmId)) {
            /* MOBI_SYS: system "customer" has no MLM ID */
            $byCustDebit = QTransGet::AS_DWNL_DEBIT . '.' . EDwnlCust::A_MLM_ID . ' IS NULL';
            $byCustCredit = QTransGet::AS_DWNL_CREDIT . '.' . EDwnlCust::A_MLM_ID . ' IS NULL';
            $byCust = "($byCustDebit) OR ($byCustCredit)";
        } else {
            /* regular customer */
            $byCustDebit = QTransGet::AS_DWNL_DEBIT . '.' . EDwnlCust::A_MLM_ID . '=:' . self::BND_MLM_ID;
            $byCustCredit = QTransGet::AS_DWNL_CREDIT . '.' . EDwnlCust::A_MLM_ID . '=:' . self::BND_MLM_ID;
            $byCust = "($byCustDebit) OR ($byCustCredit)";
        }
        $byAsset = QTransGet::AS_TYPE_ASSET . '.' . ETypeAsset::A_CODE . '=:' . self::BND_ASSET_CODE;
        $byDateFrom = QTransGet::AS_TRANS . '.' . ETransaction::A_DATE_APPLIED . '>=:' . self::BND_DATE_FROM;
        $byDateTo = QTransGet::AS_TRANS . '.' . ETransaction::A_DATE_APPLIED . '<:' . self::BND_DATE_TO;
        $byPeriod = "($byDateFrom) AND ($byDateTo)";
        $where = "($byCust) AND ($byPeriod) AND ($byAsset)";
        $query->where($where);
        $bind = [
            self::BND_ASSET_CODE => $assetTypeCode,
            self::BND_DATE_FROM => $dateFrom,
            self::BND_DATE_TO => $dateToNext
        ];
        if (!is_null($mlmId)) {
            $bind[self::BND_MLM_ID] = $mlmId;
        }
        $rs = $conn->fetchAll($query, $bind);

        /** compose result */
        $result = [];
        foreach ($rs as $one) {
            $debitMlmId = $one[QTransGet::A_DEBIT_MLM_ID] ?? Cfg::CUST_SYS_NAME;
            $creditMlmId = $one[QTransGet::A_CREDIT_MLM_ID] ?? Cfg::CUST_SYS_NAME;
            $nameFirst = $one[self::A_DEBT_CUST_FIRST];
            $nameLast = $one[self::A_DEBT_CUST_LAST];
            $debitName = trim("$nameFirst $nameLast");
            $nameFirst = $one[self::A_CRED_CUST_FIRST];
            $nameLast = $one[self::A_CRED_CUST_LAST];
            $creditName = trim("$nameFirst $nameLast");

            $item = new DItem();
            $item->setAssetTypeCode($one[QTransGet::A_ASSET_TYPE_CODE]);
            $item->setCreditAccId($one[QTransGet::A_CREDIT_ACC_ID]);
            $item->setCreditMlmId($creditMlmId);
            $item->setCreditName($creditName);
            $item->setDateApplied($one[QTransGet::A_DATE_APPLIED]);
            $item->setDatePerformed($one[QTransGet::A_DATE_PERFORMED]);
            $item->setDebitAccId($one[QTransGet::A_DEBIT_ACC_ID]);
            $item->setDebitMlmId($debitMlmId);
            $item->setDebitName($debitName);
            $item->setOperId($one[QTransGet::A_OPER_ID]);
            $item->setOperTypeCode($one[QTransGet::A_OPER_TYPE_CODE]);
            $item->setTransAmount($one[QTransGet::A_TRANS_AMOUNT]);
            $item->setTransId($one[QTransGet::A_TRANS_ID]);
            $item->setTransNote($one[QTransGet::A_TRANS_NOTE]);
            $result[] = $item;
        }
        return $result;
    }
}