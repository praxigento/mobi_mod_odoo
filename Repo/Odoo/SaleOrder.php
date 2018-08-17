<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo;

class SaleOrder
{
    const ODOO_DATA = 'data';
    const ROUTE = '/api/sale_order';

    /** @var  \Magento\Framework\Webapi\ServiceInputProcessor */
    protected $mageSrvInProc;
    /** @var \Magento\Framework\Webapi\ServiceOutputProcessor */
    protected $mageSrvOutProc;
    /** @var  \Praxigento\Odoo\Repo\Odoo\Connector\Rest */
    protected $rest;

    public function __construct(
        \Magento\Framework\Webapi\ServiceOutputProcessor $mageSrvOutProc,
        \Magento\Framework\Webapi\ServiceInputProcessor $mageSrvInProc,
        \Praxigento\Odoo\Repo\Odoo\Connector\Rest $rest
    ) {
        $this->mageSrvOutProc = $mageSrvOutProc;
        $this->mageSrvInProc = $mageSrvInProc;
        $this->rest = $rest;
    }

    protected function _convertToUnderScored($data)
    {
        $result = [];
        $array = is_array($data) ? $data : get_object_vars($data); // stdObj is not an array
        foreach ($array as $key => $item) {
            $underKey = $this->_fromCamelCase($key);
            if (is_array($item)) {
                foreach ($item as $subKey => $subItem) {
                    if (!is_int($subKey)) {
                        $subKey = $this->_fromCamelCase($subKey);
                    }
                    if ($subItem instanceof \Praxigento\Core\Data) {
                        $subData = $this->_convertToUnderScored($subItem->get());
                    } else {
                        $subData = $this->_convertToUnderScored($subItem);
                    }
                    $result[$underKey][$subKey] = $subData;
                }
            } elseif ($item instanceof \Praxigento\Core\Data) {
                $result[$underKey] = $this->_convertToUnderScored($item->get());
            } else {
                $result[$underKey] = $item;
            }
        }
        return $result;
    }

    /**
     * Convert CamelCase string to underscored string.
     *
     * Special thanks for 'cletus' (http://stackoverflow.com/questions/1993721/how-to-convert-camelcase-to-camel-case)
     *
     * @param string $input
     * @return string
     */
    protected function _fromCamelCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    public function save($order)
    {
        /* prepare request parameters */
        $orderData = $order->get();
        $underscored = $this->_convertToUnderScored($orderData);
        /* perform request and extract result data */
        $cover = $this->rest->request($underscored, self::ROUTE);
        $data = $cover->getResultData();
        if ($data) {
            $result = $this->mageSrvInProc->convertValue($data, \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Response::class);
        } else {
            $error = $cover->getError();
            $result = $this->mageSrvInProc->convertValue($error, \Praxigento\Odoo\Repo\Odoo\Data\Error::class);
            /** TODO : delete tmp code (cannot use getData as getter for property) */
            $debug = $error['data']['debug'];
            $name = $error['data']['name'];
            $result->setDebug($debug);
            $result->setName($name);
        }
        return $result;
    }
}