<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account\Saldo\Response\Data\Item;

class Asset
    extends \Praxigento\Core\Data
{
    const ASSET_TYPE = 'assetType';
    const SALDO = 'saldo';

    /**
     * @return string
     */
    public function getAssetType()
    {
        $result = parent::get(self::ASSET_TYPE);
        return $result;
    }

    /**
     * @return float
     */
    public function getSaldo()
    {
        $result = parent::get(self::SALDO);
        return $result;
    }

    /**
     * @param string $data
     * @return void
     */
    public function setAssetType($data)
    {
        parent::set(self::ASSET_TYPE, $data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setSaldo($data)
    {
        parent::set(self::SALDO, $data);
    }

}