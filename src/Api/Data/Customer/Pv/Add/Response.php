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

    public function setData(\Praxigento\Odoo\Api\Data\Customer\Pv\Add\Response\Data $data)
    {
        parent::setData($data);
    }

}