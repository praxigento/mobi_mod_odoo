<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product;

use Praxigento\Odoo\Service\Replicate\Product\Save\Request as ARequest;
use Praxigento\Odoo\Service\Replicate\Product\Save\Response as AResponse;
use Praxigento\Santegra\Config as Cfg;

/**
 * Module's internal service to save product inventory data from Odoo into Magento.
 */
class Save
{
    private const EMAIL_IDENTITY_FROM = 'support';
    private const EMAIL_TMPL = 'prxgt_odoo_product_replication_alert';

    /** @var \Magento\Backend\App\ConfigInterface */
    private $config;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Psr\Log\LoggerInterface */
    private $loggerBase;
    /** @var \Praxigento\Santegra\App\Mail\Template\TransportBuilder */
    private $mailTransportBuilder;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Lots */
    private $ownLots;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product */
    private $ownProd;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Warehouses */
    private $ownWrhs;

    public function __construct(
        \Magento\Backend\App\ConfigInterface $config,
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Psr\Log\LoggerInterface $loggerBase,
        \Praxigento\Santegra\App\Mail\Template\TransportBuilder $mailTransportBuilder,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Lots $ownLots,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product $ownProd,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Warehouses $ownWrhs
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->loggerBase = $loggerBase;
        $this->mailTransportBuilder = $mailTransportBuilder;
        $this->ownLots = $ownLots;
        $this->ownProd = $ownProd;
        $this->ownWrhs = $ownWrhs;
    }

    /**
     * @param ARequest $request
     * @return AResponse
     * @throws \Exception
     */
    public function exec($request)
    {
        assert($request instanceof ARequest);
        $processed = $total = 0;
        try {
            /** define local working data */
            /** @var  $inventory \Praxigento\Odoo\Repo\Odoo\Data\Inventory */
            $inventory = $request->getInventory();
            $warehouses = $inventory->getWarehouses();
            $lots = $inventory->getLots();
            $products = $inventory->getProducts();

            $total = count($products);
            $this->logger->info("Odoo products replication is started. Total products: $total.");
            /** perform processing */
            $this->ownWrhs->execute($warehouses);
            $this->ownLots->execute($lots);
            if (is_array($products)) {
                foreach ($products as $prod) {
                    try {
                        $this->ownProd->execute($prod);
                        $processed++;
                    } catch (\Throwable $e) {
                        $this->logger->error($e->getMessage());
                        break; // break loop on error and print out the message
                    }
                }
            }
            $this->logger->info("Products been replicated: $processed from $total.");
            $this->logger->info("Odoo products replication is completed.");
            if ($processed != $total) {
                $this->sendAlert($total, $processed);
            }
        } catch (\Throwable $e) {
            $this->sendAlert($total, $processed, $e);
        }
        /** compose result */
        $result = new AResponse();
        return $result;
    }

    private function sendAlert($total, $processed, $e = null)
    {
        $msg = "Product replication is failed. Only '$processed' products from '$total' were replicated.";
        if ($e) {
            $msg .= ' Reason: ' . $e->getMessage();
        }
        $this->loggerBase->critical($msg);
        try {
            $opts = [
                'area' => 'frontend',
                'store' => Cfg::DEF_STORE_ID_DEFAULT
            ];

            $email = $this->config->getValue(Cfg::CFG_PATH_TRANS_EMAIL_IDENT_GENERAL_EMAIL);
            $name = $this->config->getValue(Cfg::CFG_PATH_TRANS_EMAIL_IDENT_GENERAL_NAME);

            $this->mailTransportBuilder->setTemplateIdentifier(self::EMAIL_TMPL);
            $this->mailTransportBuilder->setTemplateOptions($opts);
            $this->mailTransportBuilder->setTemplateVars([]);
            $this->mailTransportBuilder->addTo($email, $name);
            $this->mailTransportBuilder->setFromByScope(self::EMAIL_IDENTITY_FROM);
            $transport = $this->mailTransportBuilder->getTransport();
            $transport->sendMessage();

            $this->loggerBase->info("Odoo product replication alert is sent.");
        } catch (\Throwable $e) {
            $this->loggerBase->error('Cannot send email alert (Odoo products replication): ' . $e->getMessage());
        }
    }
}
