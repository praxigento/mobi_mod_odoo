<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder;

class Customer
    extends \Praxigento\Core\Data
{
    /**
     * @return string
     */
    public function getGroupCode()
    {
        $result = parent::getGroupCode();
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
     * @return string
     */
    public function getIdMlm()
    {
        $result = parent::getIdMlm();
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
    public function setGroupCode($data)
    {
        parent::setGroupCode($data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setIdMage($data)
    {
        parent::setIdMage($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setIdMlm($data)
    {
        parent::setIdMlm($data);
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