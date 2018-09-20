<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Account\Daily\Response;

class Item
    extends \Praxigento\Core\Data
{
    const CODE = 'code';
    const VALUE = 'value';

    /**
     * @see https://confluence.prxgt.com/x/AwA2CQ
     *
     * @return string
     */
    public function getCode()
    {
        $result = parent::get(self::CODE);
        return $result;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        $result = parent::get(self::VALUE);
        return $result;
    }

    /**
     * @see https://confluence.prxgt.com/x/AwA2CQ
     *
     * @param string $data
     * @return void
     */
    public function setCode($data)
    {
        parent::set(self::CODE, $data);
    }

    /**
     * @param float $data
     * @return void
     */
    public function setValue($data)
    {
        parent::set(self::VALUE, $data);
    }
}