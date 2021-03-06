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
        \Praxigento\Odoo\Repo\Odoo\Inventory $daoOdoo,
        \Praxigento\Odoo\Service\Replicate\Product\Save $servSave
    ) {
        parent::__construct(
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
            'Comma-separated list of warehouses\' codes to replicate (./magento prxgt:odoo:replicate-products -w CNTR,WEST,...); if missed - products for all warehouses will be replicated;'
        );
    }

    private function parseOptProdIds(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $opt = $input->getOption(static::OPT_PROD_IDS);
        if (is_null($opt)) {
            $result = null;
            $this->logInfo('List of all products will be pulled from Odoo.');
        } else {
            $result = explode(',', $opt);
            array_walk($result, function (&$item) {
                $item = (int)$item;
            });
            $this->logInfo("Products with Odoo IDs ($opt) will be pulled from Odoo.");
        }
        return $result;
    }

    private function parseOptWrhsIds(\Symfony\Component\Console\Input\InputInterface $input)
    {
        $opt = $input->getOption(static::OPT_WRHS_IDS);
        if (is_null($opt)) {
            $result = null;
            $this->logInfo('List of products from all warehouses will be pulled from Odoo.');
        } else {
            $result = explode(',', $opt);
            $this->logInfo("Products from these warehouses ($opt) will be pulled from Odoo.");
        }
        return $result;
    }

    protected function process(\Symfony\Component\Console\Input\InputInterface $input)
    {
        /* parse arguments */
        $optProdIds = $this->parseOptProdIds($input);
        $optWrhsIds = $this->parseOptWrhsIds($input);
        /* get inventory data from Odoo */
        $inventory = $this->daoOdoo->get($optProdIds, $optWrhsIds);
        /* call service operation */
        $req = new \Praxigento\Odoo\Service\Replicate\Product\Save\Request();
        $req->setInventory($inventory);
        $this->servSave->exec($req);
    }
}