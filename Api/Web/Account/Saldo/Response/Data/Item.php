<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account\Saldo\Response\Data;

class Item
    extends \Praxigento\Core\Data
{
    const ASSETS = 'assets';
    const MLM_ID = 'mlmId';

    /**
     * @return \Praxigento\Odoo\Api\Web\Account\Saldo\Response\Data\Item\Asset[]
     */
    public function getAssets()
    {
        $result = parent::get(self::ASSETS);
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
     * @param \Praxigento\Odoo\Api\Web\Account\Saldo\Response\Data\Item\Asset[] $data
     * @return void
     */
    public function setAssets($data)
    {
        parent::set(self::ASSETS, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setMlmId($data)
    {
        parent::set(self::MLM_ID, $data);
    }

}