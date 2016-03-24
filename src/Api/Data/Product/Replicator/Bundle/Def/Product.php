<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\IProduct;

class Product extends DataObject implements IProduct
{
    const PRICE = 'Price';
    const PV = 'Pv';
    const SKU = 'Sku';

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        $result = $this->getData(self::PRICE);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getPv()
    {
        $result = $this->getData(self::PV);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getSku()
    {
        $result = $this->getData(self::SKU);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function setPrice($data)
    {
        parent::setData(self::PRICE, $data);
    }

    /**
     * @inheritdoc
     */
    public function setPv($data)
    {
        parent::setData(self::PV, $data);
    }

    /**
     * @inheritdoc
     */
    public function setSku($data)
    {
        parent::setData(self::SKU, $data);
    }

}