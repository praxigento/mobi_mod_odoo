<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Api\Def;

use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Data\Api\IBundle;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Bundle extends DataObject implements IBundle
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
    public function getLots()
    {
        $result = parent::getLots();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getOption()
    {
        $result = parent::getOptions();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getProducts()
    {
        $result = parent::getProducts();
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
    public function setCategories($data = null)
    {
        parent::setCategories($data);
    }

    /**
     * @inheritdoc
     */
    public function setLots($data = null)
    {
        parent::setLots($data);
    }

    /**
     * @inheritdoc
     */
    public function setOption($data = null)
    {
        parent::setOptions($data);
    }

    /**
     * @inheritdoc
     */
    public function setProducts($data = null)
    {
        parent::setProducts($data);
    }

    /**
     * @inheritdoc
     */
    public function setWarehouses($data = null)
    {
        parent::setWarehouses($data);
    }
}