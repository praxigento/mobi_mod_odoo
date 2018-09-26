<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data;

class Invoice
    extends \Praxigento\Core\Data
{

    /**
     * @return int
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
        return $result;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $result = parent::getStatus();
        return $result;
    }

    /**
     * @param int $data
     * @return void
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setStatus($data)
    {
        parent::setStatus($data);
    }

}