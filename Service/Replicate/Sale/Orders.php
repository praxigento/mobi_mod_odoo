<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;


class Orders
    extends \Praxigento\Core\App\Service\Base\Call
    implements \Praxigento\Odoo\Service\Replicate\Sale\IOrders
{
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\IOrder */
    protected $callOrder;
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\Orders\Collector */
    protected $subCollector;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Service\Replicate\Sale\IOrder $callOrder,
        \Praxigento\Odoo\Service\Replicate\Sale\Orders\Collector $subCollector
    ) {
        parent::__construct($logger, $manObj);
        $this->callOrder = $callOrder;
        $this->subCollector = $subCollector;
    }

    public function exec(\Praxigento\Odoo\Service\Replicate\Sale\Orders\Request $req)
    {
        $result = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Response();
        $orders = $this->subCollector->getOrdersToReplicate();
        $count = count($orders);
        $this->logger->info("There are $count orders to push to Odoo.");
        $entries = [];
        foreach ($orders as $order) {
            $req = new \Praxigento\Odoo\Service\Replicate\Sale\Order\Request();
            $req->setSaleOrder($order);
            /** @var \Praxigento\Odoo\Service\Replicate\Sale\Order\Response $resp */
            $resp = $this->callOrder->exec($req);
            $respOdoo = $resp->getOdooResponse();
            $entry = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Response\Entry();
            $id = $order->getEntityId();
            $number = $order->getIncrementId();
            $entry->setIdMage($id);
            $entry->setNumber($number);
            if ($respOdoo instanceof \Praxigento\Odoo\Data\Odoo\Error) {
                $entry->setIsSucceed(false);
                $debug = $respOdoo->getDebug();
                $name = $respOdoo->getName();
                $entry->setDebug($debug);
                $entry->setErrorName($name);
                $msg = "Cannot push sale order #$number (id:$id) to Odoo. Reason: $name ($debug).";
                $this->logger->error($msg);
            } else {
                $entry->setIsSucceed(true);
                $this->logger->info("Sale order #$number (id:$id) is pushed to Odoo.");
            }
            $entries[] = $entry;
        }
        $result->setEntries($entries);
        return $result;
    }


}