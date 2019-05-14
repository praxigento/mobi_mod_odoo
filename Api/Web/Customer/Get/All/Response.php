<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Odoo\Api\Web\Customer\Get\All;

class Response
    extends \Praxigento\Core\Api\App\Web\Response
{
    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Customer[]
     */
    public function getData()
    {
        return parent::getData();
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Customer[] $data
     * @return void
     */
    public function setData($data)
    {
        parent::setData($data);
    }

}