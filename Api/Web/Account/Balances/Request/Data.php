<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Api\Web\Account\Balances\Request;

/**
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Data
    extends \Praxigento\Core\Data
{
    const ASSET_TYPE_CODE = 'assetTypeCode';
    const CUSTOMERS = 'customers';
    const DATE_FROM = 'dateFrom';
    const DATE_TO = 'dateTo';

    /**
     * @return string
     */
    public function getAssetTypeCode()
    {
        $result = parent::get(self::ASSET_TYPE_CODE);
        return $result;
    }

    /**
     * @return string[]
     */
    public function getCustomers()
    {
        $result = parent::get(self::CUSTOMERS);
        return $result;
    }

    /**
     * @return string
     */
    public function getDateFrom()
    {
        $result = parent::get(self::DATE_FROM);
        return $result;
    }

    /**
     * @return string
     */
    public function getDateTo()
    {
        $result = parent::get(self::DATE_TO);
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
     * @param string[] $data
     * @return void
     */
    public function setCustomers($data)
    {
        parent::set(self::CUSTOMERS, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDateFrom($data)
    {
        parent::set(self::DATE_FROM, $data);
    }

    /**
     * @param string $data
     * @return void
     */
    public function setDateTo($data)
    {
        parent::set(self::DATE_TO, $data);
    }

}