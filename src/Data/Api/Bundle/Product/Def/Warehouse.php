<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Api\Bundle\Product\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Data\Api\Bundle\Product\IWarehouse;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Warehouse extends DataObject implements IWarehouse
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
    public function getLots()
    {
        $result = parent::getLots();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        $result = parent::getPrice();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPv()
    {
        $result = parent::getPv();
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
    public function setLots($data)
    {
        parent::setLots($data);
    }

    /**
     * @inheritdoc
     */
    public function setPrice($data)
    {
        parent::setPrice($data);
    }

    /**
     * @inheritdoc
     */
    public function setPv($data)
    {
        parent::setPv($data);
    }
}