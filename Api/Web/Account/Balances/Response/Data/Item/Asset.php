<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account\Balances\Response\Data\Item;

class Asset
    extends \Praxigento\Core\Data
{
    const CLOSE = 'close';
    const OPEN = 'open';
    const TYPE = 'type';

    /**
     * @return float
     */
    public function getClose()
    {
        $result = parent::get(self::CLOSE);
        return $result;
    }

    /**
     * @return float
     */
    public function getOpen()
    {
        $result = parent::get(self::OPEN);
        return $result;
    }

    /**
     * @return string
     */
    public function getType()
    {
        $result = parent::get(self::TYPE);
        return $result;
    }

    /**
     * @param float $data
     * @return void
     */
    public function setClose($data)
    {
        parent::set(self::CLOSE, $data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setOpen($data)
    {
        parent::set(self::OPEN, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setType($data)
    {
        parent::set(self::TYPE, $data);
    }
}