<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account\Daily\Response;

class Data
    extends \Praxigento\Core\Data
{
    const DATES = 'dates';

    /**
     * @return \Praxigento\Odoo\Api\Web\Account\Daily\Response\Data\Item[]
     */
    public function getDates()
    {
        $result = parent::get(self::DATES);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Daily\Response\Data\Item[] $data
     * @return void
     */
    public function setDates($data)
    {
        parent::set(self::DATES, $data);
    }
}