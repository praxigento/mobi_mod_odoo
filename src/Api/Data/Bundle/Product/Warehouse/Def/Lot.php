<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle\Product\Warehouse\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\Bundle\Product\Warehouse\ILot;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Lot extends DataObject implements ILot
{
    /**
     * @inheritdoc
     */
    public function getId()
    {
        $result = parent::getId();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getQty()
    {
        $result = parent::getQty();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function setId($data)
    {
        parent::setId($data);
    }

    /**
     * @inheritdoc
     */
    public function setQty($data)
    {
        parent::setQty($data);
    }
}