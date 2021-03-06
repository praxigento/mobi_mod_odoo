<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Customer\Pv\Add\Request;

/**
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Data
    extends \Praxigento\Core\Data
{
    const CUSTOMER_MLM_ID = 'customerMlmId';
    const DATE_APPLIED = 'dateApplied';
    const NOTES = 'notes';
    const ODOO_REF = 'odooRef';
    const PV = 'pv';

    /** @return string */
    public function getCustomerMlmId()
    {
        $result = parent::get(self::CUSTOMER_MLM_ID);
        return $result;
    }

    /** @return string|null */
    public function getDateApplied()
    {
        $result = parent::get(self::DATE_APPLIED);
        return $result;
    }

    /** @return string */
    public function getNotes()
    {
        $result = parent::get(self::NOTES);
        return $result;
    }

    /**
     * Reference to the corresponded operation in Odoo (to prevent doubling).
     *
     * @return string
     */
    public function getOdooRef()
    {
        $result = parent::get(self::ODOO_REF);
        return $result;
    }

    /**
     * Amount of the PV to add to customer balance.
     *
     * @return float
     */
    public function getPv()
    {
        $result = parent::get(self::PV);
        return $result;
    }

    /**
     * @param string
     * @return void
     */
    public function setCustomerMlmId($data)
    {
        parent::set(self::CUSTOMER_MLM_ID, $data);
    }

    /**
     * @param string
     * @return void
     */
    public function setDateApplied($data)
    {
        parent::set(self::DATE_APPLIED, $data);
    }

    /**
     * Reference to the corresponded operation in Odoo (to prevent doubling).
     *
     * @param string
     * @return void
     */
    public function setNotes($data)
    {
        parent::set(self::NOTES, $data);
    }

    /**
     * @param string
     * @return void
     */
    public function setOdooRef($data)
    {
        parent::set(self::ODOO_REF, $data);
    }

    /**
     * @param string
     * @return void
     */
    public function setPv($data)
    {
        parent::set(self::PV, $data);
    }
}