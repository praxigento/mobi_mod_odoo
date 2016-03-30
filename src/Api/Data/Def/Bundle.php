<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Def;

use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\IBundle;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Bundle extends DataObject implements IBundle
{
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
    public function getOptions()
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
    public function setLots($data = null)
    {
        parent::setLots($data);
    }

    /**
     * @inheritdoc
     */
    public function setOptions($data = null)
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