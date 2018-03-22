<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Account;

use Praxigento\Core\Api\App\Web\Response\Result as WResult;
use Praxigento\Odoo\Api\Web\Account\Daily\Request as WRequest;
use Praxigento\Odoo\Api\Web\Account\Daily\Response as WResponse;
use Praxigento\Odoo\Api\Web\Account\Daily\Response\Data as WData;
use Praxigento\Odoo\Config as Cfg;


/**
 * API adapter for internal service to get account turnover summary by day & transaction type (Odoo replication).
 */
class Daily
    implements \Praxigento\Odoo\Api\Web\Account\DailyInterface
{

    /** @var \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Accounting\Repo\Entity\Balance */
    private $repoBalance;
    /** @var \Praxigento\Accounting\Repo\Entity\Type\Asset */
    private $repoTypeAsset;
    /** @var \Praxigento\Odoo\Service\Replicate\Account\Daily */
    private $servReportDaily;

    public function __construct(
        \Praxigento\Accounting\Repo\Entity\Balance $repoBalance,
        \Praxigento\Accounting\Repo\Entity\Type\Asset $repoTypeAsset,
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Odoo\Service\Replicate\Account\Daily $servReportDaily
    ) {
        $this->repoBalance = $repoBalance;
        $this->repoTypeAsset = $repoTypeAsset;
        $this->hlpPeriod = $hlpPeriod;
        $this->servReportDaily = $servReportDaily;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Daily\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Daily\Response
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof WRequest);
        /** define local working data */
        $data = $request->getData();
        $period = $data->getPeriod();
        $from = $period->getFrom();
        $to = $period->getTo();
        $max = $this->getMaxBalanceDate();
        $max = min($to, $max);

        $respRes = new WResult();
        $respData = new WData();

        /** perform processing */
        $req = new \Praxigento\Odoo\Service\Replicate\Account\Daily\Request();
        $date = $from;
        $items = [];
        while ($date <= $max) {
            $req->setDate($date);
            $resp = $this->servReportDaily->exec($req);
            $nested = $resp->getItems();
            if (count($nested) > 0) {
                $item = new \Praxigento\Odoo\Api\Web\Account\Daily\Response\Data\Item();
                $item->setDate($date);
                $item->setItems($nested);
                $items[$date] = $item;
            }
            $date = $this->hlpPeriod->getPeriodNext($date);
        }
        $respData->setDates($items);
        $respRes->setCode(WResponse::CODE_SUCCESS);

        /** compose result */
        $result = new WResponse();
        $result->setResult($respRes);
        $result->setData($respData);
        return $result;
    }

    /**
     * Get datestamp for maximal date with calculated balance for WALLET active.
     *
     * @return string YYYYMMDD
     */
    private function getMaxBalanceDate()
    {
        $assetTypeId = $this->repoTypeAsset->getIdByCode(Cfg::CODE_TYPE_ASSET_WALLET_ACTIVE);
        $result = $this->repoBalance->getMaxDate($assetTypeId);
        return $result;
    }
}