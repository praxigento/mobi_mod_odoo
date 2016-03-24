<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\IOption;

class Option extends DataObject implements IOption
{
    const CURRENCY = 'currency';

    public function getCurrency()
    {
        $result = $this->getData(self::CURRENCY);
        return $result;
    }

    public function setCurrency($data)
    {
        parent::setData(self::CURRENCY, $data);
    }
}