<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\Bundle\IProduct;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Product extends DataObject implements IProduct
{
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
    public function getSku()
    {
        $result = parent::getSku();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getWarehouses()
    {
        $result = parent::getWarehouses();
        return $result;
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

    /**
     * @inheritdoc
     */
    public function setSku($data)
    {
        parent::setSku($data);
    }

    /**
     * @inheritdoc
     */
    public function setWarehouses($data)
    {
        parent::setWarehouses($data);
    }
}