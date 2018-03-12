<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Sales\Order;

use Praxigento\Odoo\Api\Web\Sales\Order\PushRepeat\Request as ARequest;
use Praxigento\Odoo\Api\Web\Sales\Order\PushRepeat\Response as AResponse;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PushRepeat
    implements \Praxigento\Odoo\Api\Web\Sales\Order\PushRepeatInterface
{
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\IOrders */
    private $servReplicateOrders;

    public function __construct(
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Odoo\Service\Replicate\Sale\IOrders $servReplicateOrders
    ) {
        $this->logger = $logger;
        $this->manTrans = $manTrans;
        $this->servReplicateOrders = $servReplicateOrders;

    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $baseResult = new \Praxigento\Core\Api\App\Web\Response\Result();
        $baseResult->setCode(AResponse::CODE_FAILED);
        $dataResp = new \Praxigento\Odoo\Api\Web\Sales\Order\PushRepeat\Response\Data();

        /** perform processing */
        $def = $this->manTrans->begin();
        try {
            $this->logger->info("Sales orders push action is requested.");
            $req = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Request();
            $resp = $this->servReplicateOrders->exec($req);
            $entries = $resp->getEntries();
            $dataResp->setEntries($entries);
            $this->logger->info("Sales orders push action is completed.");
            $this->manTrans->commit($def);
            $baseResult->setCode(AResponse::CODE_SUCCESS);
        } finally {
            /* rollback uncommitted transactions on exception */
            $this->manTrans->end($def);
        }
        /** compose result */
        $result = new AResponse();
        $result->setResult($baseResult);
        $result->setData($dataResp);
        return $result;
    }

}