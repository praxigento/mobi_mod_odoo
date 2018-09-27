<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\Error;

class Data
    extends \Praxigento\Core\Data
{
    /**
     * @return string
     */
    public function getDebug()
    {
        $result = parent::getDebug();
        return $result;
    }

    /**
     * @return string
     */
    public function getExceptionType()
    {
        $result = parent::getExceptionType();
        return $result;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        $result = parent::getMessage();
        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $result = parent::getName();
        return $result;
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDebug($data)
    {
        parent::setDebug($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setExceptionType($data)
    {
        parent::setExceptionType($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setMessage($data)
    {
        parent::setMessage($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setName($data)
    {
        parent::setName($data);
    }

}