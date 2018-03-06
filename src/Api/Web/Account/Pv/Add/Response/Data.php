<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account\Pv\Add\Response;

class Data
    extends \Praxigento\Core\Data
{
    const DATES = 'dates';

    /**
     * @return string
     */
    public function getDates()
    {
        $result = parent::get(self::DATES);
        return $result;
    }

    /**
     * @param string $data
     */
    public function setDates($data)
    {
        parent::set(self::DATES, $data);
    }
}