<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account\Daily\Request\Data;

/**
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Period
    extends \Praxigento\Core\Data
{
    const FROM = 'from';
    const TO = 'to';

    /**
     * @return string 'YYYYMMDD'
     */
    public function getFrom()
    {
        $result = parent::get(self::FROM);
        return $result;
    }

    /**
     * @return string 'YYYYMMDD'
     */
    public function getTo()
    {
        $result = parent::get(self::TO);
        return $result;
    }

    /**
     * @param string $data 'YYYYMMDD'
     * @return void
     */
    public function setFrom($data)
    {
        parent::set(self::FROM, $data);
    }

    /**
     * @param string $data 'YYYYMMDD'
     * @return void
     */
    public function setTo($data)
    {
        parent::set(self::TO, $data);
    }
}