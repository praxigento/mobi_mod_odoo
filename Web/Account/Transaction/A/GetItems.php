<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account\Transaction\A;

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

    /** Bound variables names ('camelCase' naming) */
    const BND_ASSET_CODE = 'assetCode';
    const BND_DATE_FROM = 'dateFrom';
    const BND_DATE_TO = 'dateTo';
    const BND_MLM_ID = 'mlmId';

    /** @var \Praxigento\Downline\Repo\Query\Account\Trans\Get */
    private $qTransGet;

    public function __construct(
        \Praxigento\Downline\Repo\Query\Account\Trans\Get $qTransGet
    ) {
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

        /** perform processing: add filters to query */
        $byCustDebit = QTransGet::AS_DWNL_DEBIT . '.' . EDwnlCust::A_MLM_ID . '=:' . self::BND_MLM_ID;
        $byCustCredit = QTransGet::AS_DWNL_CREDIT . '.' . EDwnlCust::A_MLM_ID . '=:' . self::BND_MLM_ID;
        $byCust = "($byCustDebit) OR ($byCustCredit)";
        $byAsset = QTransGet::AS_TYPE_ASSET . '.' . ETypeAsset::A_CODE . '=:' . self::BND_ASSET_CODE;
        $byDateFrom = QTransGet::AS_TRANS . '.' . ETransaction::A_DATE_APPLIED . '>=:' . self::BND_DATE_FROM;
        $byDateTo = QTransGet::AS_TRANS . '.' . ETransaction::A_DATE_APPLIED . '<:' . self::BND_DATE_TO;
        $byPeriod = "($byDateFrom) AND ($byDateTo)";
        $where = "($byCust) AND ($byPeriod) AND ($byAsset)";
        $query->where($where);
        $bind = [
            self::BND_ASSET_CODE => $assetTypeCode,
            self::BND_MLM_ID => $mlmId,
            self::BND_DATE_FROM => $dateFrom,
            self::BND_DATE_TO => $dateToNext
        ];
        $rs = $conn->fetchAll($query, $bind);

        /** compose result */
        $result = [];
        foreach ($rs as $one) {
            $debitMlmId = $one[QTransGet::A_DEBIT_MLM_ID] ?? Cfg::CUST_SYS_NAME;
            $creditMlmId = $one[QTransGet::A_CREDIT_MLM_ID] ?? Cfg::CUST_SYS_NAME;

            $item = new DItem();
            $item->setAssetTypeCode($one[QTransGet::A_ASSET_TYPE_CODE]);
            $item->setCreditAccId($one[QTransGet::A_CREDIT_ACC_ID]);
            $item->setCreditMlmId($creditMlmId);
            $item->setDateApplied($one[QTransGet::A_DATE_APPLIED]);
            $item->setDatePerformed($one[QTransGet::A_DATE_PERFORMED]);
            $item->setDebitAccId($one[QTransGet::A_DEBIT_ACC_ID]);
            $item->setDebitMlmId($debitMlmId);
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