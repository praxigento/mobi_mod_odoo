<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale\Orders\Response;

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
     * @return int
     */
    public function getIdMage()
    {
        $result = parent::getIdMage();
        return $result;
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
     * @return string
     */
    public function getNumber()
    {
        $result = parent::getNumber();
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
     * @param string $data
     */
    public function setErrorName($data)
    {
        parent::setErrorName($data);
    }

    /**
     * @param int $data
     */
    public function setIdMage($data)
    {
        parent::setIdMage($data);
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
     * @param string $data
     */
    public function setNumber($data)
    {
        parent::setNumber($data);
    }
}