<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Odoo;

use Flancer32\Lib\DataObject;

class Payment extends DataObject
{

    public function getAmount()
    {
        $result = parent::getAmount();
        return $result;
    }

    public function getType()
    {
        $result = parent::getType();
        return $result;
    }

    public function setAmount($data = null)
    {
        parent::setAmount($data);
    }

    public function setType($data = null)
    {
        parent::setType($data);
    }

}