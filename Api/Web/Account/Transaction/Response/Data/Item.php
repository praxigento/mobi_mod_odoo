<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account\Transaction\Response\Data;

class Item
    extends \Praxigento\Core\Data
{
    const ASSET_TYPE_CODE = 'assetTypeCode';
    const CREDIT_ACC_ID = 'creditAccId';
    const CREDIT_MLM_ID = 'creditMlmId';
    const CREDIT_NAME = 'creditName';
    const DATE_APPLIED = 'dateApplied';
    const DATE_PERFORMED = 'datePerformed';
    const DEBIT_ACC_ID = 'debitAccId';
    const DEBIT_MLM_ID = 'debitMlmId';
    const DEBIT_NAME = 'debitName';
    const OPER_ID = 'operId';
    const OPER_NOTE = 'operNote';
    const OPER_TYPE_CODE = 'operTypeCode';
    const TRANS_AMOUNT = 'transAmount';
    const TRANS_ID = 'transId';
    const TRANS_NOTE = 'transNote';


    /**
     * @return string
     */
    public function getAssetTypeCode()
    {
        $result = parent::get(self::ASSET_TYPE_CODE);
        return $result;
    }

    /**
     * @return int
     */
    public function getCreditAccId()
    {
        $result = parent::get(self::CREDIT_ACC_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getCreditMlmId()
    {
        $result = parent::get(self::CREDIT_MLM_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getCreditName()
    {
        $result = parent::get(self::CREDIT_NAME);
        return $result;
    }

    /**
     * @return string
     */
    public function getDateApplied()
    {
        $result = parent::get(self::DATE_APPLIED);
        return $result;
    }

    /**
     * @return string
     */
    public function getDatePerformed()
    {
        $result = parent::get(self::DATE_PERFORMED);
        return $result;
    }

    /**
     * @return int
     */
    public function getDebitAccId()
    {
        $result = parent::get(self::DEBIT_ACC_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getDebitMlmId()
    {
        $result = parent::get(self::DEBIT_MLM_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getDebitName()
    {
        $result = parent::get(self::DEBIT_NAME);
        return $result;
    }

    /**
     * @return int
     */
    public function getOperId()
    {
        $result = parent::get(self::OPER_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getOperNote()
    {
        $result = parent::get(self::OPER_NOTE);
        return $result;
    }

    /**
     * @return string
     */
    public function getOperTypeCode()
    {
        $result = parent::get(self::OPER_TYPE_CODE);
        return $result;
    }

    /**
     * @return float
     */
    public function getTransAmount()
    {
        $result = parent::get(self::TRANS_AMOUNT);
        return $result;
    }

    /**
     * @return int
     */
    public function getTransId()
    {
        $result = parent::get(self::TRANS_ID);
        return $result;
    }

    /**
     * @return string
     */
    public function getTransNote()
    {
        $result = parent::get(self::TRANS_NOTE);
        return $result;
    }

    /**
     * @param string $data
     * @return void
     */
    public function setAssetTypeCode($data)
    {
        parent::set(self::ASSET_TYPE_CODE, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setCreditAccId($data)
    {
        parent::set(self::CREDIT_ACC_ID, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setCreditMlmId($data)
    {
        parent::set(self::CREDIT_MLM_ID, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setCreditName($data)
    {
        parent::set(self::CREDIT_NAME, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDateApplied($data)
    {
        parent::set(self::DATE_APPLIED, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDatePerformed($data)
    {
        parent::set(self::DATE_PERFORMED, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setDebitAccId($data)
    {
        parent::set(self::DEBIT_ACC_ID, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDebitMlmId($data)
    {
        parent::set(self::DEBIT_MLM_ID, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDebitName($data)
    {
        parent::set(self::DEBIT_NAME, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setOperId($data)
    {
        parent::set(self::OPER_ID, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setOperNote($data)
    {
        parent::set(self::OPER_NOTE, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setOperTypeCode($data)
    {
        parent::set(self::OPER_TYPE_CODE, $data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setTransAmount($data)
    {
        parent::set(self::TRANS_AMOUNT, $data);
    }

    /**
     * @param int $data
     * @return void
     */
    public function setTransId($data)
    {
        parent::set(self::TRANS_ID, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setTransNote($data)
    {
        parent::set(self::TRANS_NOTE, $data);
    }

}