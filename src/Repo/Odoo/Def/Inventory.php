<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Def;

use Magento\Framework\Webapi\ServiceInputProcessor;
use Praxigento\Odoo\Repo\Odoo\Connector\Rest;

class Inventory
    implements \Praxigento\Odoo\Repo\Odoo\IInventory
{
    const ODOO_IDS = 'ids';
    const ROUTE = '/api/inventory';
    /** @var  ServiceInputProcessor */
    protected $_mageSrvInProc;
    /** @var  Rest */
    protected $_rest;

    public function __construct(
        ServiceInputProcessor $mageSrvInProc,
        Rest $rest
    ) {
        $this->_mageSrvInProc = $mageSrvInProc;
        $this->_rest = $rest;
    }

    /** @inheritdoc */
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
        $cover = $this->_rest->request($params, self::ROUTE);
        $data = $cover->getResultData();
        $result = $this->_mageSrvInProc->convertValue($data, \Praxigento\Odoo\Data\Odoo\Inventory::class);
        return $result;
    }
}