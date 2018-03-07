<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account\Pv\Add\Request;

/**
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Data
    extends \Praxigento\Core\Data
{
    const AMOUNT = 'amount';
    const MLM_ID = 'mlmId';
    const NOTES = 'notes';

    /**
     * @return float
     */
    public function getAmount()
    {
        $result = parent::get(self::AMOUNT);
        return $result;
    }

    /**
     * @return string
     */
    public function getMlmId()
    {
        $result = parent::get(self::MLM_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        $result = parent::get(self::NOTES);
        return $result;
    }

    /**
     * @param float
     */
    public function setAmount($data)
    {
        parent::set(self::AMOUNT, $data);
    }

    /**
     * @param string
     */
    public function setMlmId($data)
    {
        parent::set(self::MLM_ID, $data);
    }

    /**
     * @param string
     */
    public function setNotes($data)
    {
        parent::set(self::NOTES, $data);
    }
}