<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account\Transaction;

/**
 * Response to get accounting transactions data.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Response
    extends \Praxigento\Core\Api\App\Web\Response
{
    /**
     * @return \Praxigento\Odoo\Api\Web\Account\Transaction\Response\Data
     */
    public function getData()
    {
        $result = parent::get(self::A_DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Transaction\Response\Data $data
     * @return void
     */
    public function setData($data)
    {
        parent::set(self::A_DATA, $data);
    }

}