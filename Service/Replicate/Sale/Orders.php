<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;


class Orders
{
    /** @var \Praxigento\Core\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\Order */
    private $servOrder;
    /** @var \Praxigento\Odoo\Helper\Replicate\Orders\Collector */
    private $subCollector;

    public function __construct(
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Odoo\Helper\Replicate\Orders\Collector $hlpCollector,
        \Praxigento\Odoo\Service\Replicate\Sale\Order $servOrder
    ) {
        $this->logger = $logger;
        $this->subCollector = $hlpCollector;
        $this->servOrder = $servOrder;
    }

    public function exec(\Praxigento\Odoo\Service\Replicate\Sale\Orders\Request $req)
    {
        $result = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Response();
        $orders = $this->subCollector->getOrdersToReplicate();
        $count = count($orders);
        $this->logger->info("There are $count orders to push to Odoo.");
        $entries = [];
        foreach ($orders as $order) {
            $id = $order->getEntityId();
            $number = $order->getIncrementId();
            $this->logger->info("Push sale order #$number (id:$id) into Odoo.");
            try {
                $req = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Request();
                $req->setSaleOrder($order);
                /** @var \Praxigento\Odoo\Service\Replicate\Sale\Order\Response $resp */
                $resp = $this->servOrder->exec($req);
                $respOdoo = $resp->getOdooResponse();
                $entry = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Response\Entry();
                $entry->setIdMage($id);
                $entry->setNumber($number);
                if ($respOdoo instanceof \Praxigento\Odoo\Repo\Odoo\Data\Error) {
                    $entry->setIsSucceed(false);
                    $debug = $respOdoo->getDebug();
                    $name = $respOdoo->getName();
                    $entry->setDebug($debug);
                    $entry->setErrorName($name);
                    $msg = "Cannot push sale order #$number (id:$id) to Odoo. Reason: $name ($debug).";
                    $this->logger->error($msg);
                } elseif($resp->isSucceed()) {
                    $entry->setIsSucceed(true);
                    $this->logger->info("Sale order #$number (id:$id) is pushed into Odoo.");
                } else {
                    $this->logger->info("Sale order #$number (id:$id) is processed.");
                }
                $entries[] = $entry;
            } catch (\Throwable $e) {
                $this->logger->error("Cannot push sale order #$number (id:$id) into Odoo. Error: "
                    . $e->getMessage());
            }
        }
        $result->setEntries($entries);
        return $result;
    }


}
