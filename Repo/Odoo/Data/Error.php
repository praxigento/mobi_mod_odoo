<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data;

class Error
    extends \Praxigento\Core\Data
{
    /**
     * @return int
     */
    public function getCode()
    {
        $result = parent::getCode();
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Repo\Odoo\Data\Error\Data
     */
    public function getData()
    {
        $result = parent::getData();
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
     * @param int $data
     * @return void
     */
    public function setCode($data)
    {
        parent::setCode($data);
    }

    /**
     * @param \Praxigento\Odoo\Repo\Odoo\Data\Error\Data $data
     * @return void
     */
    public function setData($data)
    {
        parent::setData($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setMessage($data)
    {
        parent::setMessage($data);
    }
}