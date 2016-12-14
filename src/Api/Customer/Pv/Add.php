<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Customer\Pv;


class Add
    implements \Praxigento\Odoo\Api\Customer\Pv\AddInterface
{
    public function execute(\Praxigento\Odoo\Api\Data\Customer\Pv\Add\Request $data)
    {
        $result = new \Praxigento\Odoo\Api\Data\Customer\Pv\Add\Response();
        /* parse request data */
        $customerMlmId = $data->getCustomerMlmId();
        $pv = $data->getPv();
        $dateApplied = $data->getDateApplied();
        $odooRef = $data->getOdooRef();
        /* process request data */
        /* compose response */
        $result->setOdooRef($odooRef);
        return $result;
    }

}