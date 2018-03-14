<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Web\Customer\Wallet\Balance;

/**
 * Get balance for customer wallet.
 */
class Response
    extends \Praxigento\Core\Api\App\Web\Response
{
    const CODE_CUSTOMER_IS_NOT_FOUND = 'CUSTOMER_IS_NOT_FOUND';
    const CODE_DUPLICATED = 'DUPLICATED';

    /**
     * @return \Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Response\Data|null
     */
    public function getData()
    {
        $result = parent::get(self::ATTR_DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Response\Data $data
     */
    public function setData($data)
    {
        parent::set(self::ATTR_DATA, $data);
    }

}