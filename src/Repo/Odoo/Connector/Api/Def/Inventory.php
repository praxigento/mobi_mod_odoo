<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Api\Def;

use Praxigento\Odoo\Repo\Odoo\Connector\Api\IInventory;
use Praxigento\Odoo\Repo\Odoo\Connector\Base\RestRequest;

class Inventory implements IInventory
{
    const ODOO_IDS = 'ids';
    const ROUTE = '/api/inventory';
    /** @var  RestRequest */
    private $_rest;

    public function __construct(
        RestRequest $rest
    ) {
        $this->_rest = $rest;
    }

    /**
     * @inheritdoc
     */
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
        $result = $cover->getResultData();
        return $result;
    }
}