<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\Inventory;

/**
 * Lot that is related to products bundle.
 *
 * This class is used in REST/SOAP API and all methods should be declared explicitly.
 *
 */
class Lot
    extends \Praxigento\Core\Data
{
    /**
     * Get expiration date for all products from this lot.
     *
     * @return string
     */
    public function getExpirationDate()
    {
        $result = parent::getExpirationDate();
        return $result;
    }

    /**
     * Get Odoo ID of the lot.
     *
     * @return  int|null
     */
    public function getIdOdoo()
    {
        $result = parent::getIdOdoo();
        return $result;
    }

    /**
     * Get code number used by humans.
     *
     * @return string
     */
    public function getNumber()
    {
        $result = parent::getNumber();
        return $result;
    }

    /**
     * Set expiration date for all products from this lot.
     *
     * @param string $data
     * @return void
     */
    public function setExpirationDate($data)
    {
        parent::setExpirationDate($data);
    }

    /**
     * Set Odoo ID of the lot.
     *
     * @param int $data
     * @return void
     */
    public function setIdOdoo($data)
    {
        parent::setIdOdoo($data);
    }

    /**
     * Set code number used by humans.
     *
     * @param string $data
     * @return void
     */
    public function setNumber($data)
    {
        parent::setNumber($data);
    }
}