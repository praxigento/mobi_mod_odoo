<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Cli\Cmd\Replicate;

class Products
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /**
     * #@+
     * Arguments names
     */
    const ARG_IDS = 'ids';
    /**#@- */
    /** @var \Praxigento\Odoo\Service\IReplicate */
    protected $callReplicate;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Service\IReplicate $callReplicate
    ) {
        parent::__construct(
            $manObj,
            'prxgt:odoo:replicate:products',
            'Pull products list from Odoo and replicate data into Magento.'
        );
        $this->callReplicate = $callReplicate;
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
    )
    {
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
        /* call service operation */
        $req = new \Praxigento\Odoo\Service\Replicate\Request\ProductsFromOdoo();
        $req->setOdooIds($ids);
        /** @var \Praxigento\Odoo\Service\Replicate\Response\ProductsFromOdoo $resp */
        $resp = $this->callReplicate->productsFromOdoo($req);
        $succeed = $resp->isSucceed();
        if ($succeed) {
            $output->writeln('<info>Command is completed.<info>');
        } else {
            $output->writeln('<info>Command is failed.<info>');
        }
    }
}