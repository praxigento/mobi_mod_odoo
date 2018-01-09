<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Def;

class Inventory
    implements \Praxigento\Odoo\Repo\Odoo\IInventory
{
    const ODOO_IDS = 'ids';
    const ROUTE = '/api/inventory';

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

    public function get($ids = null)
    {
        /* prepare request parameters */
        if (is_array($ids)) {
            $params = [self::ODOO_IDS => $ids];
        } elseif (is_int($ids)) {
            $params = [self::ODOO_IDS => [$ids]];
        } else {
            $params = [self::ODOO_IDS => []];
        }
        /* perform request and extract result data */
        $cover = $this->rest->request($params, self::ROUTE);
        $data = $cover->getResultData();
        $result = $this->inputProcessor->convertValue($data, \Praxigento\Odoo\Data\Odoo\Inventory::class);
        return $result;
    }
}