<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Web\Customer\Pv\Add;

/**
 * Add PV to customer balance.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Response
    extends \Praxigento\Core\Api\App\Web\Response
{
    const CODE_CUSTOMER_IS_NOT_FOUND = 'CUSTOMER_IS_NOT_FOUND';
    const CODE_DUPLICATED = 'DUPLICATED';

    /**
     * @return \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response\Data|null
     */
    public function getData()
    {
        $result = parent::get(self::A_DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response\Data $data
     * @return void
     */
    public function setData($data)
    {
        parent::set(self::A_DATA, $data);
    }

}