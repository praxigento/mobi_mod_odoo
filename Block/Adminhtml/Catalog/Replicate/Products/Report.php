<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Block\Adminhtml\Catalog\Replicate\Products;


class Report
    extends \Magento\Backend\Block\Template
{
    const FIELDSET = 'replicate_product';
    const FIELD_SKU = 'sku';
    const FIELD_WRHS = 'wrhs';
    const NO_WRHS = \Praxigento\Odoo\Block\Adminhtml\Catalog\Replicate\Products\Index::NO_WRHS;
    /** @var \Praxigento\Odoo\Repo\Odoo\Inventory */
    private $daoOdoo;
    /** @var \Praxigento\Odoo\Repo\Dao\Product */
    private $daoProd;
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save */
    private $servReplicate;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Praxigento\Odoo\Repo\Dao\Product $daoProd,
        \Praxigento\Odoo\Repo\Odoo\Inventory $daoOdoo,
        \Praxigento\Odoo\Service\Replicate\Product\Save $servReplicate,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logger = $logger;
        $this->repoProd = $repoProd;
        $this->daoProd = $daoProd;
        $this->daoOdoo = $daoOdoo;
        $this->servReplicate = $servReplicate;
    }

    protected function _beforeToHtml()
    {
        [$sku, $wrhs] = $this->parseParams();
        $inventory = $this->getOdooInventory($sku, $wrhs);
        $req = new \Praxigento\Odoo\Service\Replicate\Product\Save\Request();
        $req->setInventory($inventory);
        $this->servReplicate->exec($req);
        $result = parent::_beforeToHtml();
        return $result;
    }

    private function getOdooInventory($sku, $wrhs)
    {
        $allSku = explode(',', $sku);
        $ids = [];
        foreach ($allSku as $one) {
            try {
                $prod = $this->repoProd->get(trim($one));
                $mageId = $prod->getId();
                /** @var \Praxigento\Odoo\Repo\Data\Product $registry */
                $registry = $this->daoProd->getById($mageId);
                $odooId = $registry->getOdooRef();
                $ids[] = $odooId;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                /* do nothing */
            }
        }
        if ($wrhs == (string)self::NO_WRHS) $wrhs = null;
        $result = $this->daoOdoo->get($ids, $wrhs);
        return $result;
    }

    public function outLog()
    {
        $hndl = $this->logger->getHandlerMemory();
        $stream = $hndl->getStream();
        if ($stream) {
            rewind($stream);
            $result = stream_get_contents($stream);
        } else {
            $result = __('No data.');
        }
        return $result;
    }

    private function parseParams()
    {
        $request = $this->getRequest();
        $params = $request->getParams();
        $sku = '';
        $wrhs = self::NO_WRHS;
        if (isset($params[self::FIELDSET])) {
            $fieldset = $params[self::FIELDSET];
            $sku = $fieldset[self::FIELD_SKU] ?? '';
            $wrhs = $fieldset[self::FIELD_WRHS] ?? self::NO_WRHS;
            if ($wrhs == \Praxigento\Odoo\Ui\DataProvider\Options\Warehouse::VAL_ALL)
                $wrhs = self::NO_WRHS;
        }
        return [$sku, $wrhs];
    }
}