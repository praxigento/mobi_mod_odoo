<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Account;

use Praxigento\Odoo\Api\Web\Account\Daily\Request as ARequest;
use Praxigento\Odoo\Api\Web\Account\Daily\Response as AResponse;

/**
 * API adapter for internal service to get account turnover summary by day & transaction type (Odoo replication).
 */
class Daily
    implements \Praxigento\Odoo\Api\Web\Account\DailyInterface
{

    /** @var \Praxigento\Core\Api\Helper\Period */
    private $hlpPeriod;
    /** @var \Praxigento\Odoo\Service\Replicate\Account\Daily */
    private $servReportDaily;

    public function __construct(
        \Praxigento\Core\Api\Helper\Period $hlpPeriod,
        \Praxigento\Odoo\Service\Replicate\Account\Daily $servReportDaily
    ) {
        $this->hlpPeriod = $hlpPeriod;
        $this->servReportDaily = $servReportDaily;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $period = $data->getPeriod();
        $from = $period->getFrom();
        $to = $period->getTo();
        $max = $this->getMaxBalanceDate();

        /** perform processing */
        $req = new \Praxigento\Odoo\Service\Replicate\Account\Daily\Request();
        $date = $from;
        while ($date < $to) {
            $req->setDate($date);
            $resp = $this->servReportDaily->exec($req);
            $date = $this->hlpPeriod->getPeriodNext($date);
        }

        /** compose result */
        $result = new AResponse();
        $result->setData($resp);
        return $result;
    }

    private function getMaxBalanceDate()
    {
    }
}