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
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Service\Replicate\Sale\Orders $srvReplicateOrders
    ) {
        parent::__construct(
            $manObj,
            'prxgt:odoo:replicate:orders',
            'Push sale orders that are not replicated into Odoo.'
        );
        $this->srvReplicateOrders = $srvReplicateOrders;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $output->writeln("<info>Command '" . $this->getName() . "' is started.<info>");
        $this->checkAreaCode();
        $req = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Request();
        $resp = $this->srvReplicateOrders->exec($req);
        $entries = $resp->getEntries();
        foreach ($entries as $entry) {
            $id = $entry->getIdMage();
            $success = $entry->getIsSucceed();
            if ($success) {
                $output->writeln("<info>Order #$id is pushed up to Odoo.<info>");
            } else {
                $error = $entry->getErrorName();
                $debug = $entry->getDebug();
                $output->writeln("<info>Order #$id is not pushed up to Odoo. Reason: $error<info>");
                $output->writeln("<error>$debug<error>");
            }
        }
        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }
}