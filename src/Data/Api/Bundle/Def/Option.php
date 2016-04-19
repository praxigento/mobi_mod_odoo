<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Api\Bundle\Def;

use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Data\Api\Bundle\IOption;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Option extends DataObject implements IOption
{
    public function getCurrency()
    {
        $result = parent::getCurrency();
        return $result;
    }

    public function setCurrency($data)
    {
        parent::setCurrency($data);
    }
}