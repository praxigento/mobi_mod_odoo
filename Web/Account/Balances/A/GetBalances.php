<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account\Balances\A;

use Praxigento\Accounting\Api\Repo\Query\Balance\OnDate\Closing as QBalance;
use Praxigento\Accounting\Repo\Data\Account as EAccount;
use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;
use Praxigento\Core\App\Repo\Query\Expression as AnExpression;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;
use Praxigento\Odoo\Api\Web\Account\Balances\Response\Data\Item as DItem;

/**
 * Retrieve balances data (open|close) from DB.
 */
class GetBalances
{
    /** Tables aliases */
    private const AS_DWNL_CUST = 'dwnlCust';
    private const AS_TYPE_ASSET = 'typeAsset';

    /** Columns/expressions aliases */
    const A_ASSET_TYPE = 'assetType';
    const A_MLM_ID = 'mlmId';

    /** Bound variables names ('camelCase' naming) */
    private const BND_ASSET_CODE = 'assetCode';

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

    private function composeExpressionMlmIds($mlmIds)
    {
        $conn = $this->resource->getConnection();
        $ids = '';
        foreach ($mlmIds as $one) {
            $mlmId = $conn->quote($one);
            $ids .= $mlmId . ',';
        }
        $ids = substr($ids, 0, -1);
        $exp = self::AS_DWNL_CUST . '.' . EDwnlCust::A_MLM_ID . " IN ($ids)";
        $result = new AnExpression($exp);
        return $result;
    }

    /**
     * @param string $assetTypeCode
     * @param string[] $mlmIds
     * @param string $dateFrom
     * @param string $dateTo
     * @return \Praxigento\Odoo\Api\Web\Account\Balances\Response\Data\Item[]
     */
    public function exec($assetTypeCode, $mlmIds, $dateFrom, $dateTo)
    {
        /** define local working data */
        $dsFrom = $this->hlpPeriod->getPeriodForDate($dateFrom); // YYYYMMDD
        $dsFrom = $this->hlpPeriod->getPeriodPrev($dsFrom); // prev. closing = current open
        $dsTo = $this->hlpPeriod->getPeriodForDate($dateTo); // YYYYMMDD

        /** perform processing */
        $open = $this->getBalance($dsFrom, $mlmIds, $assetTypeCode);
        $close = $this->getBalance($dsTo, $mlmIds, $assetTypeCode);

        /** compose result */
        $result = [];
        foreach ($mlmIds as $mlmId) {
            $item = new DItem();
            $item->setMlmId($mlmId);
            if (isset($open[$mlmId])) {
            }

            $result[$mlmId] = $item;
        }
        return $result;
    }

    private function getAssets($open, $close)
    {
        $result = [];
        foreach ($open as $asset => $balance) {
            if (!in_array($asset, $result)) $result[] = $asset;
        }
        foreach ($open as $asset => $balance) {
            if (!in_array($asset, $result)) $result[] = $asset;
        }
        return $result;
    }

    /**
     * Get closing balance for given date/customer/asset.
     *
     * @param string $date
     * @param string[] $mlmIds
     * @param string $assetCode
     * @return array [$mlmId][$asset][$balance]
     */
    private function getBalance($date, $mlmIds, $assetCode)
    {
        $filterByAsset = !is_null($assetCode);
        $query = $this->populateBalanceQuery($mlmIds, $filterByAsset);
        $conn = $query->getConnection();

        /** perform processing: add filters to query */
        $bind = [
            QBalance::BND_MAX_DATE => $date
        ];
        if ($filterByAsset) {
            $bind[self::BND_ASSET_CODE] = $assetCode;
        }
        $rs = $conn->fetchAll($query, $bind);

        /** compose result */
        $result = [];
        foreach ($rs as $one) {
            $mlmId = $one[self::A_MLM_ID];
            $asset = $one[self::A_ASSET_TYPE];
            $balance = $one[QBalance::A_BALANCE];
            $result[$mlmId][$asset] = $balance;
        }
        return $result;
    }

    /**
     * Set additional filters to base query.
     *
     * @param string[] $mlmIds
     * @param bool $filterByAsset
     * @return \Magento\Framework\DB\Select
     */
    private function populateBalanceQuery($mlmIds, $filterByAsset)
    {
        $result = $this->qBalance->build();
        $asAcc = QBalance::AS_ACC;
        $asTypeAsset = self::AS_TYPE_ASSET;
        $asDwnl = self::AS_DWNL_CUST;

        /* LEFT JOIN prxgt_acc_type_asset */
        $tbl = $this->resource->getTableName(ETypeAsset::ENTITY_NAME);
        $as = $asTypeAsset;
        $cols = [
            self::A_ASSET_TYPE => ETypeAsset::A_CODE
        ];
        $cond = "$as." . ETypeAsset::A_ID . "=$asAcc." . EAccount::A_ASSET_TYPE_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* LEFT JOIN prxgt_dwnl_customer */
        $tbl = $this->resource->getTableName(EDwnlCust::ENTITY_NAME);
        $as = $asDwnl;
        $cols = [
            self::A_MLM_ID => EDwnlCust::A_MLM_ID
        ];
        $cond = "$as." . EDwnlCust::A_CUSTOMER_ID . "=$asAcc." . EAccount::A_CUST_ID;
        $result->joinLeft([$as => $tbl], $cond, $cols);

        /* WHERE */
        $byMlmIds = $this->composeExpressionMlmIds($mlmIds);
        $result->where($byMlmIds);
        if ($filterByAsset) {
            $byType = "$asTypeAsset." . ETypeAsset::A_CODE . "=:" . self::BND_ASSET_CODE;
            $result->where($byType);
        }

        return $result;
    }
}