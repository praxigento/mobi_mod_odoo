<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account\Transaction\A;

use Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing as QBalance;
use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;

/**
 * Retrieve balances data (open|close) from DB.
 */
class GetBalances
{
    /** Tables aliases for external usage ('camelCase' naming) */
    private const AS_DWNL_CUST = 'dwnlCust';
    private const AS_TYPE_ASSET = 'typeAsset';

    /** Bound variables names ('camelCase' naming) */
    private const BND_ASSET_CODE = 'assetCode';
    private const BND_DATE = 'date';
    private const BND_MLM_ID = 'mlmId';

    /** @var \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing */
    private $qBalance;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing $qBalance,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod
    ) {
        $this->resource = $resource;
        $this->qBalance = $qBalance;
        $this->hlpPeriod = $hlpPeriod;
    }

    public function exec($assetTypeCode, $mlmId, $dateFrom, $dateTo)
    {
        /** define local working data */
        $dsFrom = $this->hlpPeriod->getPeriodForDate($dateFrom); // YYYYMMDD
        $dsFrom = $this->hlpPeriod->getPeriodPrev($dsFrom); // prev. closing = current open
        $dsTo = $this->hlpPeriod->getPeriodForDate($dateTo); // YYYYMMDD

        /** perform processing */
        $balanceOpen = $this->getBalance($dsFrom, $mlmId, $assetTypeCode);
        $balanceClose = $this->getBalance($dsTo, $mlmId, $assetTypeCode);

        return [$balanceOpen, $balanceClose];
    }

    /**
     * Get closing balance for given date/customer/asset.
     *
     * @param string $date
     * @param string $mlmId
     * @param string $assetCode
     * @return float
     */
    private function getBalance($date, $mlmId, $assetCode)
    {
        $query = $this->populateBalanceQuery();
        $conn = $query->getConnection();
        /** perform processing: add filters to query */
        $bind = [
            QBalance::BND_MAX_DATE => $date,
            self::BND_MLM_ID => $mlmId,
            self::BND_ASSET_CODE => $assetCode
        ];
        $rs = $conn->fetchAll($query, $bind);

        /** compose result */
        $result = 0;
        if (
            is_array($rs) &&
            (count($rs) == 1)
        ) {
            $item = reset($rs);
            $result = $item[QBalance::A_BALANCE];
        }
        return $result;
    }

    /**
     * Set additional filters to base query.
     *
     * @return \Magento\Framework\DB\Select
     */
    private function populateBalanceQuery()
    {
        $result = $this->qBalance->build();
        $asAcc = QBalance::AS_ACC;
        $asTypeAsset = self::AS_TYPE_ASSET;
        $asDwnl = self::AS_DWNL_CUST;

        /* LEFT JOIN prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(ETypeAsset::ENTITY_NAME);
        $as = $asTypeAsset;
        $cols = [];
        $cond = "$as." . ETypeAsset::A_ID . "=$asAcc." . EAccount::A_ASSET_TYPE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer */
        $tbl = $this->resource->getTableName(EDwnlCust::ENTITY_NAME);
        $as = $asDwnl;
        $cols = [];
        $cond = "$as." . EDwnlCust::A_CUSTOMER_ID . "=$asAcc." . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* WHERE */
        $byType = "$asTypeAsset." . ETypeAsset::A_CODE . "=:" . self::BND_ASSET_CODE;
        $byMlmId = "$asDwnl." . EDwnlCust::A_MLM_ID . "=:" . self::BND_MLM_ID;
        $result->where("($byType) AND ($byMlmId)");

        return $result;
    }
}