<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo;

class Inventory
{
    const PROD_IDS = 'prod_ids';
    const ROUTE = '/api/inventory';
    const WRHS_IDS = 'wrhs_ids';

    /** @var  \Magento\Framework\Webapi\ServiceInputProcessor */
    private $inputProcessor;
    /** @var  \Praxigento\Odoo\Repo\Odoo\Connector\Rest */
    private $rest;

    public function __construct(
        \Magento\Framework\Webapi\ServiceInputProcessor $inputProcessor,
        \Praxigento\Odoo\Repo\Odoo\Connector\Rest $rest
    ) {
        $this->inputProcessor = $inputProcessor;
        $this->rest = $rest;
    }

    public function get($prodIds = null, $wrhsIds = null)
    {
        /* prepare request parameters */
        if (is_array($prodIds)) {
            $argProdIds = $prodIds;
        } elseif (is_int($prodIds)) {
            $argProdIds = [$prodIds];
        } else {
            $argProdIds = [];
        }
        if (is_array($wrhsIds)) {
            $argWrhsIds = $wrhsIds;
        } elseif (is_int($wrhsIds)) {
            $argWrhsIds = [$wrhsIds];
        } else {
            $argWrhsIds = [];
        }
        $params = [
            self::PROD_IDS => $argProdIds,
            self::WRHS_IDS => $argWrhsIds
        ];
        /* perform request and extract result data */
        $cover = $this->rest->request($params, self::ROUTE);
        $data = $cover->getResultData();
        $result = $this->inputProcessor->convertValue($data, \Praxigento\Odoo\Repo\Odoo\Data\Inventory::class);
        return $result;
    }
}