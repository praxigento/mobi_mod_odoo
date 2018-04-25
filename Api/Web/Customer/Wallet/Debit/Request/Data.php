<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Customer\Wallet\Debit\Request;

/**
 * Transfer funds from customer wallet to system wallet.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Data
    extends \Praxigento\Core\Data
{
    const AMOUNT = 'amount';
    const CUSTOMER_MLM_ID = 'customerMlmId';
    const NOTES = 'notes';
    const ODOO_REF = 'odooRef';

    /**
     * Debit amount.
     *
     * @return float
     */
    public function getAmount()
    {
        $result = parent::get(self::AMOUNT);
        return $result;
    }

    /** @return string */
    public function getCustomerMlmId()
    {
        $result = parent::get(self::CUSTOMER_MLM_ID);
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

    /** @param string */
    public function setAmount($data)
    {
        parent::set(self::AMOUNT, $data);
    }

    /** @param string */
    public function setCustomerMlmId($data)
    {
        parent::set(self::CUSTOMER_MLM_ID, $data);
    }

    /**
     * Reference to the corresponded operation in Odoo (to prevent doubling).
     *
     * @param string
     */
    public function setNotes($data)
    {
        parent::set(self::NOTES, $data);
    }

    /** @param string */
    public function setOdooRef($data)
    {
        parent::set(self::ODOO_REF, $data);
    }
}