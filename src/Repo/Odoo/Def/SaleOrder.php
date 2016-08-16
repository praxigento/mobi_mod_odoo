<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Def;

class SaleOrder
    implements \Praxigento\Odoo\Repo\Odoo\ISaleOrder
{
    const ODOO_DATA = 'data';
    const ROUTE = '/api/sale_order';
    /** @var  \Magento\Framework\Webapi\ServiceInputProcessor */
    protected $_mageSrvInProc;
    /** @var  \Praxigento\Odoo\Repo\Odoo\Connector\Rest */
    protected $_rest;

    public function __construct(
        \Magento\Framework\Webapi\ServiceInputProcessor $mageSrvInProc,
        \Praxigento\Odoo\Repo\Odoo\Connector\Rest $rest
    ) {
        $this->_mageSrvInProc = $mageSrvInProc;
        $this->_rest = $rest;
    }

    /** @inheritdoc */
    public function save($order)
    {
        /* prepare request parameters */
        $underscored = $order->getData(null, true);
        /* perform request and extract result data */
        $cover = $this->_rest->request($underscored, self::ROUTE);
        $data = $cover->getResultData();
        if ($data) {
            $result = $this->_mageSrvInProc->convertValue($data, \Praxigento\Odoo\Data\Odoo\SaleOrder\Response::class);
        } else {
            $error = $cover->getError();
            $result = $this->_mageSrvInProc->convertValue($error, \Praxigento\Odoo\Data\Odoo\Error::class);
        }
        return $result;
    }
}