<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Cli\Replicate;

class Products
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    const OPT_PROD_IDS = 'prods';
    const OPT_WRHS_IDS = 'wrhs';

    /** @var \Praxigento\Odoo\Repo\Odoo\Inventory */
    private $daoOdoo;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save */
    private $servSave;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Repo\Odoo\Inventory $daoOdoo,
        \Praxigento\Odoo\Service\Replicate\Product\Save $servSave
    ) {
        parent::__construct(
            $manObj,
            'prxgt:odoo:replicate:products',
            'Pull products list from Odoo and replicate data into Magento.'
        );
        $this->daoOdoo = $daoOdoo;
        $this->servSave = $servSave;
    }

    protected function configure()
    {
        parent::configure();
        $this->addOption(
            self::OPT_PROD_IDS,
            'p',
            \Symfony\Component\Console\Input\InputArgument::OPTIONAL,
            'Comma-separated list of product\'s Odoo IDs to replicate (./magento prxgt:odoo:replicate-products -p 1,2,...); if missed - all products will be replicated;'
        );
        $this->addOption(
            self::OPT_WRHS_IDS,
            'w',
            \Symfony\Component\Console\Input\InputArgument::OPTIONAL,
            'Comma-separated list of warehouses\' codes to replicate (./magento prxgt:odoo:replicate-products -w 1,2,...); if missed - products for all warehouses will be replicated;'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $output->writeln("<info>Command '" . $this->getName() . "' is started.<info>");
        try {
            $this->checkAreaCode(); // to prevent "Area code not set" exception.
            /* parse arguments */
            $optProdIds = $this->parseOptProdIds($input, $output);
            $optWrhsIds = $this->parseOptWrhsIds($input, $output);
            /* get inventory data from Odoo */
            $inventory = $this->daoOdoo->get($optProdIds, $optWrhsIds);
            /* call service operation */
            $req = new \Praxigento\Odoo\Service\Replicate\Product\Save\Request();
            $req->setInventory($inventory);
            $this->servSave->exec($req);
        } catch (\Throwable $e) {
            $output->writeln('<info>Command \'' . $this->getName() . '\' failed. Reason: '
                . $e->getMessage() . '.<info>');
            $output->writeln('<info>' . $e->getTraceAsString() . '.<info>');
        }
        $output->writeln('<info>Command \'' . $this->getName() . '\' is completed.<info>');
    }

    private function parseOptProdIds(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $opt = $input->getOption(static::OPT_PROD_IDS);
        if (is_null($opt)) {
            $result = null;
            $output->writeln('<info>List of all products will be pulled from Odoo.<info>');
        } else {
            $result = explode(',', $opt);
            array_walk($result, function (&$item) {
                $item = (int)$item;
            });
            $output->writeln("<info>Products with Odoo IDs ($opt) will be pulled from Odoo.<info>");
        }
        return $result;
    }

    private function parseOptWrhsIds(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $opt = $input->getOption(static::OPT_WRHS_IDS);
        if (is_null($opt)) {
            $result = null;
            $output->writeln('<info>List of products from all warehouses will be pulled from Odoo.<info>');
        } else {
            $result = explode(',', $opt);
            $output->writeln("<info>Products from these warehouses ($opt) will be pulled from Odoo.<info>");
        }
        return $result;
    }
}