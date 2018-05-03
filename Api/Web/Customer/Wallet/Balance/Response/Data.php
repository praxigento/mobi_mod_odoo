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
    const CURRENCY = 'currency';

    /** @return float|null */
    public function getBalance()
    {
        $result = parent::get(self::BALANCE);
        return $result;
    }

    /** @return string */
    public function getCurrency()
    {
        $result = parent::get(self::CURRENCY);
        return $result;
    }

    /** @param float */
    public function setBalance($data)
    {
        parent::set(self::BALANCE, $data);
    }

    /** @param string */
    public function setCurrency($data)
    {
        parent::set(self::CURRENCY, $data);
    }

}