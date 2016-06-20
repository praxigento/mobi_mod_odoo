<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Odoo;

use Flancer32\Lib\DataObject;

class Invoice extends DataObject
{

    public function getId()
    {
        $result = parent::getId();
        return $result;
    }

    public function getStatus()
    {
        $result = parent::getStatus();
        return $result;
    }

}