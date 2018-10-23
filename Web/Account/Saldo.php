<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account;

use Praxigento\Accounting\Repo\Data\Type\Asset as ETypeAsset;
use Praxigento\Core\Api\App\Web\Response\Result as WResult;
use Praxigento\Downline\Repo\Data\Customer as EDwnlCust;
use Praxigento\Odoo\Api\Web\Account\Saldo\Request as WRequest;
use Praxigento\Odoo\Api\Web\Account\Saldo\Response as WResponse;
use Praxigento\Odoo\Api\Web\Account\Saldo\Response\Data as WData;
use Praxigento\Odoo\Api\Web\Account\Saldo\Response\Data\Item as DRespItem;
use Praxigento\Odoo\Api\Web\Account\Saldo\Response\Data\Item\Asset as DRespAsset;
use Praxigento\Odoo\Web\Account\Saldo\A\Repo\Query\SummaryBase as QSumBase;

/**
 * Web service implementation to get saldo for filtered transactions.
 */
class Saldo
    implements \Praxigento\Odoo\Api\Web\Account\SaldoInterface
{
    /** @var \Praxigento\Odoo\Api\Helper\BusinessCodes */
    private $hlpBusCodes;
    /** @var \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Odoo\Web\Account\Saldo\A\Repo\Query\SummaryBase */
    private $qSumBase;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Odoo\Api\Helper\BusinessCodes $hlpBusCodes,
        \Praxigento\Odoo\Web\Account\Saldo\A\Repo\Query\SummaryBase $qSumBase
    ) {
        $this->resource = $resource;
        $this->hlpPeriod = $hlpPeriod;
        $this->hlpBusCodes = $hlpBusCodes;
        $this->qSumBase = $qSumBase;
    }

    private function convertTranTypes($trnTypes)
    {
        $result = [];
        foreach ($trnTypes as $type) {
            $operTypes = $this->hlpBusCodes->getMageCodesForTransType($type);
            $result = array_merge($result, $operTypes);
        }
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Saldo\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Saldo\Response
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof WRequest);
        /** define local working data */
        $reqData = $request->getData();
        $tranTypes = $reqData->getTransTypes();
        $customers = $reqData->getCustomers();
        $dateFrom = $reqData->getDateFrom();
        $dateTo = $reqData->getDateTo();

        /** perform processing */
        $operTypes = $this->convertTranTypes($tranTypes);
        $dateFrom = substr($dateFrom, 0, 10); // YYYY-MM-DD
        $dateTo = substr($dateTo, 0, 10); // YYYY-MM-DD
        $dateToNext = date('Y-m-d', strtotime($dateTo . ' +1 day'));

        /* get summaries by customer/asset */
        $debits = $this->getSumDebits($dateFrom, $dateToNext, $customers, $operTypes);
        $credits = $this->getSumCredits($dateFrom, $dateToNext, $customers, $operTypes);

        $items = [];
        $items = $this->processDebits($items, $debits);
        $items = $this->processCredits($items, $credits);

        /** compose result */
        $respData = new WData();
        $respData->setItems($items);

        $respRes = new WResult();
        $respRes->setCode(WResponse::CODE_SUCCESS);

        $result = new WResponse();
        $result->setData($respData);
        $result->setResult($respRes);
        return $result;
    }

    /**
     * Add filters & grouping to base query to get debit summaries.
     *
     * @param $dateFrom
     * @param $dateTo
     * @param $customers
     * @param $operTypes
     * @return array
     */
    private function getSumCredits($dateFrom, $dateTo, $customers, $operTypes)
    {
        $query = $this->qSumBase->build();
        $conn = $query->getConnection();
        $bind = [
            QSumBase::BND_DATE_FROM => $dateFrom,
            QSumBase::BND_DATE_TO => $dateTo
        ];

        /* filter by customers */
        $byCustomers = $this->qSumBase->expByCustomers($customers, QSumBase::AS_CREDIT_CUST);
        $query->where($byCustomers);

        /* filter by operation types */
        if (
            is_array($operTypes) &&
            count($operTypes)
        ) {
            $byOperType = $this->qSumBase->expByOperType($operTypes);
            $query->where($byOperType);
        }

        /* group by credit customer and asset type */
        $group = [
            QSumBase::AS_CREDIT_CUST . '.' . EDwnlCust::A_MLM_ID,
            QSumBase::AS_TYPE_ASSET . '.' . ETypeAsset::A_CODE
        ];
        $query->group($group);

        $result = $conn->fetchAll($query, $bind);
        return $result;
    }

    /**
     * Add filters & grouping to base query to get debit summaries.
     *
     * @param $dateFrom
     * @param $dateTo
     * @param $customers
     * @param $operTypes
     * @return array
     */
    private function getSumDebits($dateFrom, $dateTo, $customers, $operTypes)
    {
        $query = $this->qSumBase->build();
        $conn = $query->getConnection();
        $bind = [
            QSumBase::BND_DATE_FROM => $dateFrom,
            QSumBase::BND_DATE_TO => $dateTo
        ];

        /* filter by customers */
        $byCustomers = $this->qSumBase->expByCustomers($customers, QSumBase::AS_DEBIT_CUST);
        $query->where($byCustomers);

        /* filter by operation types */
        if (
            is_array($operTypes) &&
            count($operTypes)
        ) {
            $byOperType = $this->qSumBase->expByOperType($operTypes);
            $query->where($byOperType);
        }

        /* group by debit customer and asset type */
        $group = [
            QSumBase::AS_DEBIT_CUST . '.' . EDwnlCust::A_MLM_ID,
            QSumBase::AS_TYPE_ASSET . '.' . ETypeAsset::A_CODE
        ];
        $query->group($group);

        $result = $conn->fetchAll($query, $bind);
        return $result;
    }

    /**
     * Put credit query result set to the REST response structure.
     *
     * @param DRespItem[] $items
     * @param array $credits
     * @return DRespItem[]
     * @throws \Exception
     */
    private function processCredits($items, $credits)
    {
        foreach ($credits as $one) {
            $mlmId = $one[QSumBase::A_CREDIT_CUST];
            $asset = $one[QSumBase::A_ASSET];
            $summary = $one[QSumBase::A_SUM];
            /* check items by MLM ID */
            if (isset($items[$mlmId])) {
                $item = $items[$mlmId];
            } else {
                $item = new DRespItem();
                $item->setMlmId($mlmId);
                $item->setAssets([]);
            }
            /* check selected item by asset type */
            $assets = $item->getAssets();
            if (isset($assets[$asset])) {
                $assetItem = $assets[$asset];
            } else {
                $assetItem = new DRespAsset();
                $assetItem->setAssetType($asset);
                $assetItem->setSaldo(0);
            }
            /* decrease saldo value */
            $saldo = $assetItem->getSaldo();
            $saldo += $summary;
            $assetItem->setSaldo($saldo);
            /* put values back to the structure */
            $assets[$asset] = $assetItem;
            $item->setAssets($assets);
            $items[$mlmId] = $item;
        }
        return $items;
    }

    /**
     * Put debit query result set to the REST response structure.
     *
     * @param DRespItem[] $items
     * @param array $debits
     * @return DRespItem[]
     * @throws \Exception
     */
    private function processDebits($items, $debits)
    {
        foreach ($debits as $one) {
            $mlmId = $one[QSumBase::A_DEBIT_CUST];
            $asset = $one[QSumBase::A_ASSET];
            $summary = $one[QSumBase::A_SUM];
            /* check items by MLM ID */
            if (isset($items[$mlmId])) {
                $item = $items[$mlmId];
            } else {
                $item = new DRespItem();
                $item->setMlmId($mlmId);
                $item->setAssets([]);
            }
            /* check selected item by asset type */
            $assets = $item->getAssets();
            if (isset($assets[$asset])) {
                $assetItem = $assets[$asset];
            } else {
                $assetItem = new DRespAsset();
                $assetItem->setAssetType($asset);
                $assetItem->setSaldo(0);
            }
            /* decrease saldo value */
            $saldo = $assetItem->getSaldo();
            $saldo -= $summary;
            $assetItem->setSaldo($saldo);
            /* put values back to the structure */
            $assets[$asset] = $assetItem;
            $item->setAssets($assets);
            $items[$mlmId] = $item;
        }
        return $items;
    }


}