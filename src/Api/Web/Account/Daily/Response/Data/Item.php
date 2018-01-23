<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account\Daily\Response\Data;

class Item
    extends \Praxigento\Core\Data
{
    const DATE = 'date';
    const ITEMS = 'items';

    /**
     * @return string 'YYYYMMDD'
     */
    public function getDate()
    {
        $result = parent::get(self::DATE);
        return $result;
    }

    /**
     * @return \Praxigento\Odoo\Service\Replicate\Account\Daily\Response\Item[]
     */
    public function getItems()
    {
        $result = parent::get(self::ITEMS);
        return $result;
    }

    /**
     * @param string $data 'YYYYMMDD'
     */
    public function setDate($data)
    {
        parent::set(self::DATE, $data);
    }

    /**
     * @param \Praxigento\Odoo\Service\Replicate\Account\Daily\Response\Item[] $data
     */
    public function setItems($data)
    {
        parent::set(self::ITEMS, $data);
    }
}