<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\Bundle\Def;


use Flancer32\Lib\DataObject;
use Praxigento\Odoo\Api\Data\Bundle\ICategory;

/**
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 */
class Category extends DataObject implements ICategory
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
    public function getName()
    {
        $result = parent::getName();
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getParentId()
    {
        $result = parent::getParentId();
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
    public function setName($data)
    {
        parent::setName($data);
    }

    /**
     * @inheritdoc
     */
    public function setParentId($data)
    {
        parent::setParentId($data);
    }
}