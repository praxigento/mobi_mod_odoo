<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\Bundle\IWarehouse;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Warehouse extends DataObject implements IWarehouse
{
    public function getCode()
    {
        $result = parent::getCode();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        $result = parent::getCurrency();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
        return $result;
    }

    public function setCode($data)
    {
        parent::setCode($data);
    }

    /**
     * @inheritdoc
     */
    public function setCurrency($data)
    {
        parent::setCurrency($data);
    }

    /**
     * @inheritdoc
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }
}