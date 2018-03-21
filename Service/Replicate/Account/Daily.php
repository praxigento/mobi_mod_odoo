<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Account;

use Praxigento\Accounting\Repo\Entity\Data\Transaction as ETrans;
use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Service\Replicate\Account\Daily\Own\Repo\Query\GetTransSummary as QBGetSummary;
use Praxigento\Odoo\Service\Replicate\Account\Daily\Request as ARequest;
use Praxigento\Odoo\Service\Replicate\Account\Daily\Response as AResponse;
use Praxigento\Odoo\Service\Replicate\Account\Daily\Response\Item as DItem;

/**
 * Module level service to get account turnover summary by day & transaction type (Odoo replication).
 */
class Daily
{
    /** @var int Wallet Account ID for system customer */
    private static $cacheAccIdWallet = null;

    /** @var \Praxigento\Odoo\Tool\IBusinessCodesManager */
    private $hlpCodeMgr;
    /** @var \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Odoo\Service\Replicate\Account\Daily\Own\Repo\Query\GetTransSummary */
    private $qbGetSummary;
    /** @var \Praxigento\Accounting\Repo\Entity\Account */
    private $repoAcc;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    private $repoTypeAsset;

    public function __construct(
        \Praxigento\Accounting\Repo\Entity\Account $repoAcc,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Odoo\Tool\IBusinessCodesManager $hlpCodeMgr,
        \Praxigento\Odoo\Service\Replicate\Account\Daily\Own\Repo\Query\GetTransSummary $qbGetSummary
    ) {
        $this->repoAcc = $repoAcc;
        $this->repoTypeAsset = $repoTypeAsset;
        $this->hlpPeriod = $hlpPeriod;
        $this->hlpCodeMgr = $hlpCodeMgr;
        $this->qbGetSummary = $qbGetSummary;
    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $date = $request->getDate();
        $from = $this->hlpPeriod->getTimestampFrom($date);
        $to = $this->hlpPeriod->getTimestampNextFrom($date);
        $accIdRepr = $this->getSysAccId();

        /** perform processing */
        /* get summary for incoming & outgoing transactions */
        $sumIn = $this->getSumIn($accIdRepr, $from, $to);
        $sumOut = $this->getSumOut($accIdRepr, $from, $to);
        /* merge incoming & outgoing data into one array [operType => amount] */
        $summary = [];
        $this->merge($summary, $sumIn);
        $this->merge($summary, $sumOut);
        /* classification transactions by type according to project specific requirements */
        $items = [];
        foreach ($summary as $operTypeId => $value) {
            $operTypeCode = $this->hlpCodeMgr->getBusCodeForOperTypeId($operTypeId);
            $item = new DItem();
            $item->setCode($operTypeCode);
            $item->setValue($value);
            $items[] = $item;
        }

        /** compose result */
        $result = new AResponse();
        $result->setItems($items);
        return $result;
    }

    /**
     * Get system account ID from DB or cached.
     *
     * @return int
     */
    private function getSysAccId()
    {
        if (is_null(self::$cacheAccIdWallet)) {
            $walletAssetId = $this->repoTypeAsset->getIdByCode(Cfg::CODE_TYPE_ASSET_WALLET_ACTIVE);
            self::$cacheAccIdWallet = $this->repoAcc->getSystemAccountId($walletAssetId);
        }
        return self::$cacheAccIdWallet;
    }

    /**
     * Get summary for incoming transaction from system account for period $from-$to.
     *
     * @param int $accId system account ID for credit
     * @param string $from date from (inclusive)
     * @param string $to date to (exclusive)
     * @return array
     */
    private function getSumIn($accId, $from, $to)
    {
        $query = $this->qbGetSummary->build();
        $conn = $query->getConnection();
        /* additional filter by credit account (outgoing transactions for system customer) */
        $bndByAcc = 'byAccId';
        $byAcc = QBGetSummary::AS_TRANS . '.' . ETrans::ATTR_CREDIT_ACC_ID . "=:$bndByAcc";
        $query->where($byAcc);
        /* compose bind vars */
        $bind = [
            QBGetSummary::BND_DATE_FROM => $from,
            QBGetSummary::BND_DATE_TO => $to,
            $bndByAcc => $accId
        ];
        /* get result set */
        $rs = $conn->fetchAll($query, $bind);
        /* transform to simple array */
        $result = [];
        foreach ($rs as $one) {
            $operTyprId = $one[QBGetSummary::A_OPER_TYPE];
            $value = $one[QBGetSummary::A_VALUE];
            $result[$operTyprId] = $value;
        }
        return $result;
    }

    /**
     * Get summary for outgoing transaction from system account for period $from-$to.
     *
     * @param int $accId system account ID for credit
     * @param string $from date from (inclusive)
     * @param string $to date to (exclusive)
     * @return array
     */
    private function getSumOut($accId, $from, $to)
    {
        $query = $this->qbGetSummary->build();
        $conn = $query->getConnection();
        /* additional filter by credit account (outgoing transactions for system customer) */
        $bndByAcc = 'byAccId';
        $byAcc = QBGetSummary::AS_TRANS . '.' . ETrans::ATTR_DEBIT_ACC_ID . "=:$bndByAcc";
        $query->where($byAcc);
        /* compose bind vars */
        $bind = [
            QBGetSummary::BND_DATE_FROM => $from,
            QBGetSummary::BND_DATE_TO => $to,
            $bndByAcc => $accId
        ];
        /* get result set */
        $rs = $conn->fetchAll($query, $bind);
        /* transform to simple array */
        $result = [];
        foreach ($rs as $one) {
            $operTyprId = $one[QBGetSummary::A_OPER_TYPE];
            $value = $one[QBGetSummary::A_VALUE];
            $result[$operTyprId] = (-1) * $value; // revert sign to use addition in $this->>merge();
        }
        return $result;
    }

    private function merge(&$result, $merge)
    {
        foreach ($merge as $typeId => $value) {
            if (isset($result[$typeId])) {
                $result[$typeId] += $value;
            } else {
                $result[$typeId] = $value;
            }
        }
    }
}