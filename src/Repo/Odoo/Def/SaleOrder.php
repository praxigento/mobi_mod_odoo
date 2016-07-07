<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Def;

use Magento\Framework\Webapi\ServiceInputProcessor;
use Praxigento\Odoo\Repo\Odoo\Connector\Rest;
use Praxigento\Odoo\Repo\Odoo\ISaleOrder;

class SaleOrder implements ISaleOrder
{
    const ODOO_DATA = 'data';
    const ROUTE = '/api/sale_order';
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
    public function save($order)
    {
        /* prepare request parameters */
        $underscored = $order->getData(null, true);
        $params = [self::ODOO_DATA => $underscored];
        /* perform request and extract result data */
        $cover = $this->_rest->request($underscored, self::ROUTE);
        $data = $cover->getResultData();
        $error = $cover->getError();
//        $result = $this->_mageSrvInProc->convertValue($data, IBundle::class);
        /* TODO: convert ass. array into data object */
        return $data;
    }
}