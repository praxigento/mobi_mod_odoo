<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account\Transaction\Response;

class Data
    extends \Praxigento\Core\Data
{
    const BALANCE_CLOSE = 'balanceClose';
    const BALANCE_OPEN = 'balanceOpen';
    const ITEMS = 'items';

    /**
     * @return float
     */
    public function getBalanceClose()
    {
        $result = parent::get(self::BALANCE_CLOSE);
        return $result;
    }

    /**
     * @return float
     */
    public function getBalanceOpen()
    {
        $result = parent::get(self::BALANCE_OPEN);
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Api\Web\Account\Transaction\Response\Data\Item[]
     */
    public function getItems()
    {
        $result = parent::get(self::ITEMS);
        return $result;
    }

    /**
     * @param float $data
     * @return void
     */
    public function setBalanceClose($data)
    {
        parent::set(self::BALANCE_CLOSE, $data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setBalanceOpen($data)
    {
        parent::set(self::BALANCE_OPEN, $data);
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Transaction\Response\Data\Item[] $data
     * @return void
     */
    public function setItems($data)
    {
        parent::set(self::ITEMS, $data);
    }
}