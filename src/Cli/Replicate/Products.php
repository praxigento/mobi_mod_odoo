<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Cli\Replicate;

class Products
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /**
     * #@+
     * Arguments names
     */
    const ARG_IDS = 'ids';
    /**#@- */
    /**
     * @var \Praxigento\Odoo\Service\IReplicate
     * @deprecated
     */
    private $callReplicate;
    private $repoOdoo;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save */
    private $servSave;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Service\IReplicate $callReplicate,
        \Praxigento\Odoo\Repo\Odoo\IInventory $repoOdoo,
        \Praxigento\Odoo\Service\Replicate\Product\Save $servSave
    ) {
        parent::__construct(
            $manObj,
            'prxgt:odoo:replicate:products',
            'Pull products list from Odoo and replicate data into Magento.'
        );
        $this->callReplicate = $callReplicate;
        $this->repoOdoo = $repoOdoo;
        $this->servSave = $servSave;
    }

    protected function configure()
    {
        parent::configure();
        $this->addArgument(
            'ids',
            \Symfony\Component\Console\Input\InputArgument::OPTIONAL,
            'Comma-delimited list of Odoo IDs to replicate (./magento prxgt:odoo:replicate-products 1,2,...); if missed - all products will be replicated;'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $this->checkAreaCode(); // to prevent "Area code not set" exception.
        /* parse arguments */
        $argIds = $input->getArgument(static::ARG_IDS);
        if (is_null($argIds)) {
            $ids = null;
            $output->writeln('<info>List of all products will be pulled from Odoo.<info>');
        } else {
            $ids = explode(',', $argIds);
            $output->writeln("<info>Products with Odoo IDs ($argIds) will be pulled from Odoo.<info>");
        }
        $inventory = $this->repoOdoo->get($ids);
        /* call service operation */
        $req = new \Praxigento\Odoo\Service\Replicate\Request\ProductsFromOdoo();
        $req->setOdooIds($ids);
        $req = new \Praxigento\Odoo\Service\Replicate\Product\Save\Request();
        $req->setInventory($inventory);
        $this->servSave->exec($req);
        $output->writeln('<info>Command is completed.<info>');
    }
}