<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\ILot;

class Lot extends DataObject implements ILot
{
    const EXP_DATE = 'ExpirationDate';
    const ID_ODOO = 'IdOdoo';

    /**
     * @inheritdoc
     */
    public function getExpirationDate()
    {
        $result = $this->getData(self::EXP_DATE);
        return $result;
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
    public function setExpirationDate($data)
    {
        parent::setData(self::EXP_DATE, $data);
    }

    /**
     * @inheritdoc
     */
    public function setIdOdoo($data)
    {
        parent::setData(self::ID_ODOO, $data);
    }
}