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
    public function getCategories()
    {
        $result = parent::getCategories();
        return $result;
    }

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
    public function getIsActive()
    {
        $result = parent::getIsActive();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        $result = parent::getName();
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
    public function getWeight()
    {
        $result = parent::getWeight();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function setCategories($data)
    {
        parent::setCategories($data);
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
    public function setIsActive($data)
    {
        parent::setIsActive($data);
    }

    /**
     * @inheritdoc
     */
    public function setName($data)
    {
        parent::setName($data);
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

    /**
     * @inheritdoc
     */
    public function setWeight($data)
    {
        parent::setWeight($data);
    }
}