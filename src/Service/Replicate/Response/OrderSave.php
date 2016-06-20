<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Response;


class OrderSave extends \Praxigento\Core\Service\Base\Response
{
    /**
     * @return string
     */
    public function getOdooResponse()
    {
        $result = parent::getOdooResponse();
        return $result;
    }

    /**
     * @param string $data
     */
    public function setOdooResponse($data = null)
    {
        parent::setOdooResponse($data);
    }
}