<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account\Transaction\Response\Data;

class Item
    extends \Praxigento\Core\Data
{
    const TRANS_ID = 'transId';

    /**
     * @return int
     */
    public function getTransId()
    {
        $result = parent::get(self::TRANS_ID);
        return $result;
    }

    /**
     * @param int $data
     * @return void
     */
    public function setTransId($data)
    {
        parent::set(self::TRANS_ID, $data);
    }

}