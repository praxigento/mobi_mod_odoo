<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account\Balances\Response;

class Data
    extends \Praxigento\Core\Data
{
    const ITEMS = 'items';

    /**
     * @return \Praxigento\Odoo\Api\Web\Account\Balances\Response\Data\Item[]
     */
    public function getItems()
    {
        $result = parent::get(self::ITEMS);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Balances\Response\Data\Item[] $data
     * @return void
     */
    public function setItems($data)
    {
        parent::set(self::ITEMS, $data);
    }
}