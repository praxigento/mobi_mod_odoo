<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Sales\Order;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PushRepeat
    implements \Praxigento\Odoo\Api\Sales\Order\PushRepeatInterface
{
    /** @var \Praxigento\Odoo\Service\IReplicate */
    protected $callReplicate;
    /** @var \Praxigento\Odoo\Api\Sales\Order\PushRepeat\Collector */
    protected $collector;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct(
        \Praxigento\Odoo\Fw\Logger\Odoo $logger,
        \Praxigento\Odoo\Service\IReplicate $callReplicate,
        \Praxigento\Odoo\Api\Sales\Order\PushRepeat\Collector $collector
    ) {
        $this->logger = $logger;
        $this->callReplicate = $callReplicate;
        $this->collector = $collector;
    }

    public function execute()
    {
        $result = new \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report();
        $this->logger->info("Sales orders push action is requested.");
        $orders = $this->collector->getOrdersToReplicate();
        $count = count($orders);
        $this->logger->info("There are $count orders to push to Odoo.");
        $entries = [];
        foreach ($orders as $order) {
            $req = new \Praxigento\Odoo\Service\Replicate\Request\OrderSave();
            $req->setSaleOrder($order);
            /** @var \Praxigento\Odoo\Service\Replicate\Response\OrderSave $resp */
            $resp = $this->callReplicate->orderSave($req);
            $respOdoo = $resp->getOdooResponse();
            $reportEntry = new \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report\Entry();
            $id = $order->getEntityId();
            $number = $order->getIncrementId();
            $reportEntry->setIdMage($id);
            $reportEntry->setNumber($number);
            if ($respOdoo instanceof \Praxigento\Odoo\Data\Odoo\Error) {
                $reportEntry->setIsSucceed(false);
                $debug = $respOdoo->getDebug();
                $name = $respOdoo->getName();
                $reportEntry->setDebug($debug);
                $reportEntry->setErrorName($name);
                $msg = "Cannot push sale order #$number (id:$id) to Odoo. Reason: $name ($debug).";
                $this->logger->error($msg);
            } else {
                $reportEntry->setIsSucceed(true);
                $this->logger->info("Sale order #$number (id:$id) is pushed to Odoo.");
            }
            $entries[] = $reportEntry;
        }
        $result->setEntries($entries);
        return $result;
    }

}