<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product;

use Praxigento\Odoo\Service\Replicate\Product\Save\Request as ARequest;
use Praxigento\Odoo\Service\Replicate\Product\Save\Response as AResponse;

/**
 * Module's internal service to save product inventory data from Odoo into Magento.
 */
class Save
{
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Lots */
    private $ownLots;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product */
    private $ownProd;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Warehouses */
    private $ownWrhs;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Lots $ownLots,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product $ownProd,
        \Praxigento\Odoo\Service\Replicate\Product\Save\Own\Warehouses $ownWrhs
    ) {
        $this->logger = $logger;
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
                $this->ownProd->execute($prod);
            }
        }
        $this->logger->info("Odoo products replication is completed.");

        /** compose result */
        $result = new AResponse();
        return $result;
    }
}