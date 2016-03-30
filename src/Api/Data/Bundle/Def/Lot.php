<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\Bundle\ILot;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Lot extends DataObject implements ILot
{

    /**
     * @inheritdoc
     */
    public function getExpirationDate()
    {
        $result = parent::getExpirationDate();
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

    /**
     * @inheritdoc
     */
    public function setExpirationDate($data)
    {
        parent::setExpirationDate($data);
    }

    /**
     * @inheritdoc
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }
}