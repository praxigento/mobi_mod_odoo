<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report;

/**
 * Replication results for one order.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Entry
    extends \Flancer32\Lib\Data
{
    /**
     * @return int
     */
    public function getIdMage()
    {
        $result = parent::getIdMage();
        return $result;
    }

    /**
     * @param int $data
     */
    public function setIdMage($data)
    {
        parent::setIdMage($data);
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        $result = parent::getNumber();
        return $result;
    }

    /**
     * @param string $data
     */
    public function setNumber($data)
    {
        parent::setNumber($data);
    }

    /**
     * 'true' - order is successfully saved into Odoo.
     *
     * @return bool
     */
    public function getIsSucceed()
    {
        $result = parent::getIsSucceed();
        return $result;
    }

    /**
     * 'true' - order is successfully saved into Odoo.
     *
     * @param bool $data
     */
    public function setIsSucceed($data)
    {
        parent::setIsSucceed($data);
    }

    /**
     * Debug stacktrace from Odoo.
     *
     * @return string|null
     */
    public function getDebug()
    {
        $result = parent::getDebug();
        return $result;
    }

    /**
     * Debug stacktrace from Odoo.
     *
     * @param string $data
     */
    public function setDebug($data)
    {
        parent::setDebug($data);
    }

    /**
     * Error name from Odoo.
     *
     * @return string|null
     */
    public function getErrorName()
    {
        $result = parent::getErrorName();
        return $result;
    }

    /**
     * Error name from Odoo.
     *
     * @param string $data
     */
    public function setErrorName($data)
    {
        parent::setErrorName($data);
    }
}