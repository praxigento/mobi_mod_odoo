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
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\IOrders */
    protected $callReplicateOrders;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct(
        \Praxigento\Odoo\Fw\Logger\Odoo $logger,
        \Praxigento\Odoo\Service\Replicate\Sale\IOrders $callReplicateOrders
    ) {
        $this->logger = $logger;
        $this->callReplicateOrders = $callReplicateOrders;

    }

    public function execute()
    {
        $result = new \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report();
        $this->logger->info("Sales orders push action is requested.");
        $req = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Request();
        $resp = $this->callReplicateOrders->exec($req);
        $entries = $resp->getEntries();
        $result->setEntries($entries);
        $this->logger->info("Sales orders push action is completed.");
        return $result;
    }

}