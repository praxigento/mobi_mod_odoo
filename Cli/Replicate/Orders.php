<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Cli\Replicate;

class Orders
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\Orders */
    private $srvReplicateOrders;

    public function __construct(
        \Praxigento\Odoo\Service\Replicate\Sale\Orders $srvReplicateOrders
    ) {
        parent::__construct(
            'prxgt:odoo:replicate:orders',
            'Push sale orders that are not replicated into Odoo.'
        );
        $this->srvReplicateOrders = $srvReplicateOrders;
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $req = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Request();
        $resp = $this->srvReplicateOrders->exec($req);
        $entries = $resp->getEntries();
        foreach ($entries as $entry) {
            $id = $entry->getIdMage();
            $success = $entry->getIsSucceed();
            if ($success) {
                $this->logInfo("Order #$id is pushed up to Odoo.");
            } else {
                $error = $entry->getErrorName();
                $debug = $entry->getDebug();
                $this->logError("Order #$id is not pushed up to Odoo. Reason: $error.\n$debug");
            }
        }
    }
}