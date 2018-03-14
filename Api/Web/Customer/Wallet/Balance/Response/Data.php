<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Response;

/**
 * Get balance for customer wallet.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Data
    extends \Praxigento\Core\Data
{
    const BALANCE = 'balance';

    /** @return float|null */
    public function getBalance()
    {
        $result = parent::get(self::BALANCE);
        return $result;
    }


    /** @param float */
    public function setBalance($data)
    {
        parent::set(self::BALANCE, $data);
    }

}