<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Account;

use Praxigento\Accounting\Api\Service\Account\Balance\LastDate\Request as ALastDayReq;
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
    /** @var \Praxigento\Accounting\Api\Service\Account\Balance\LastDate */
    private $servLastDate;
    /** @var \Praxigento\Odoo\Service\Replicate\Account\Daily */
    private $servReportDaily;

    public function __construct(
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Accounting\Api\Service\Account\Balance\LastDate $servLastDate,
        \Praxigento\Odoo\Service\Replicate\Account\Daily $servReportDaily
    ) {
        $this->hlpPeriod = $hlpPeriod;
        $this->servLastDate = $servLastDate;
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
     * @throws \Exception
     */
    private function getMaxBalanceDate()
    {
        $req = new ALastDayReq();
        $req->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_WALLET_ACTIVE);
        $resp = $this->servLastDate->exec($req);
        $result = $resp->getLastDate();
        return $result;
    }
}