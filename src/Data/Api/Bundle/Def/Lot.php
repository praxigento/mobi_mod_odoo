<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Data\Api\Bundle\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Data\Api\Bundle\ILot;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Lot extends DataObject implements ILot
{

    /**
     * @inheritdoc
     */
    public function getCode()
    {
        $result = parent::getCode();
        return $result;
    }

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
    public function getId()
    {
        $result = parent::getIdOdoo();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function setCode($data)
    {
        parent::setCode($data);
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
    public function setId($data)
    {
        parent::setIdOdoo($data);
    }
}