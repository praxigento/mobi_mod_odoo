<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Cli\Cmd\Replicate;

class Orders
    extends \Praxigento\Core\Cli\Cmd\Base
{

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj
    ) {
        parent::__construct(
            $manObj,
            'prxgt:odoo:replicate:orders',
            'Push sale orders that are not replicated into Odoo.'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $output->writeln('<info>Sale orders push replication (Mage2Odoo) is started.<info>');
        $output->writeln('<info>Command is completed.<info>');
    }
}