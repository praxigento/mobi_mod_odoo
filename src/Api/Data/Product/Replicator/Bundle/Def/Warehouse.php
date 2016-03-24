<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\IWarehouse;

class Warehouse extends DataObject implements IWarehouse
{
    const CURRENCY = 'Currency';
    const ID_ODOO = 'IdOdoo';

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        $result = $this->getData(self::CURRENCY);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function setCurrency($data)
    {
        parent::setData(self::CURRENCY, $data);
    }

    /**
     * @inheritdoc
     */
    public function getIdOdoo()
    {
        $result = $this->getData(self::ID_ODOO);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function setIdOdoo($data)
    {
        parent::setData(self::ID_ODOO, $data);
    }
}