<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Data\Customer\Pv\Add;

class Response
    extends \Praxigento\Core\Api\Response
{
    /**
     * @return \Praxigento\Odoo\Api\Data\Customer\Pv\Add\Response\Data|null
     */
    public function getData()
    {
        $result = parent::getData();
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Data\Customer\Pv\Add\Response\Data $data
     */
    public function setData($data)
    {
        parent::setData($data);
    }

}