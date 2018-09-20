<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Customer\Wallet\Balance;

/**
 * Get balance for customer wallet.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Request
    extends \Praxigento\Core\Api\App\Web\Request
{

    /**
     * @return \Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Request\Data
     */
    public function getData()
    {
        $result = parent::get(self::DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Request\Data $data
     * @return void
     */
    public function setData($data)
    {
        parent::set(self::DATA, $data);
    }
}