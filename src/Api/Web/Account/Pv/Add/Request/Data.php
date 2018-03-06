<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account\Pv\Add\Request;

/**
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Data
    extends \Praxigento\Core\Data
{
    const PERIOD = 'period';

    /**
     * @return string
     */
    public function getPeriod()
    {
        $result = parent::get(self::PERIOD);
        return $result;
    }

    /**
     * @param string
     */
    public function setPeriod($data)
    {
        parent::set(self::PERIOD, $data);
    }
}