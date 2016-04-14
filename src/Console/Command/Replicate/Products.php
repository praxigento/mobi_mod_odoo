<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Console\Command\Replicate;

use Magento\Framework\ObjectManagerInterface;
use Magento\Setup\Model\ObjectManagerProvider;
use Praxigento\Odoo\Service\IReplicate;
use Praxigento\Odoo\Service\Replicate\Request\ProductsFromOdoo as ProductsFromOdooRequest;
use Praxigento\Odoo\Service\Replicate\Response\ProductsFromOdoo as ProductsFromOdooResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Products extends Command
{
    /**
     * #@+
     * Arguments names
     */
    const ARG_IDS = 'ids';
    /**#@- */
    /** @var IReplicate */
    protected $_callReplicate;
    /** @var ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        ObjectManagerInterface $objectManagerProvider,
        IReplicate $callReplicate
    ) {
        parent::__construct();
        $this->_manObj = $objectManagerProvider;
        $this->_callReplicate = $callReplicate;
    }

    /**
     * Sets area code to start a session for replication.
     */
    private function _setAreaCode()
    {
        $areaCode = 'adminhtml';
        /** @var \Magento\Framework\App\State $appState */
        $appState = $this->_manObj->get(\Magento\Framework\App\State::class);
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
        $configLoader = $this->_manObj->get(\Magento\Framework\ObjectManager\ConfigLoaderInterface::class);
        $this->_manObj->configure($configLoader->load($areaCode));
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('prxgt:odoo:replicate-products');
        $this->setDescription('Pull products list from Odoo and replicate data into Magento.');
        $this->addArgument('ids', InputArgument::OPTIONAL,
            'Comma-delimited list of Odoo IDs to replicate (./magento prxgt:odoo:replicate-products 1,2,...); if missed - all products will be replicated;');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* parse arguments */
        $argIds = $input->getArgument(self::ARG_IDS);
        if (is_null($argIds)) {
            $ids = null;
            $output->writeln('<info>List of all products will be pulled from Odoo.<info>');
        } else {
            $ids = explode(',', $argIds);
            $msgIds = implode(',', $ids);
            $output->writeln("<info>Products with Odoo IDs ($msgIds) will be pulled from Odoo.<info>");
        }
        /* setup session */
        $this->_setAreaCode();
        /* call service operation */
        /** @var ProductsFromOdooRequest $req */
        $req = new ProductsFromOdooRequest();
        $req->setOdooIds($ids);
        /** @var ProductsFromOdooResponse $resp */
        $resp = $this->_callReplicate->productsFromOdoo($req);
        if ($resp->isSucceed()) {
            $output->writeln('<info>Replication is done.<info>');
        } else {
            $output->writeln('<info>Replication is failed.<info>');
        }
    }
}