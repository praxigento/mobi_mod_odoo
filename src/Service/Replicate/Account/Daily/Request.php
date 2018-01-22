<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Account\Daily;

/**
 * Request to get account turnover summary by day & transaction type (Odoo replication).
 *
 * (Define getters explicitly to use with Swagger tool)
 */
class Request
    extends \Praxigento\Core\Data
{
    /** string 'YYYYMMDD' */
    const DATE = 'date';

    /**
     * @return string 'YYYYMMDD'
     */
    public function getDate()
    {
        $result = parent::get(self::DATE);
        return $result;
    }

    /**
     * @param string $data 'YYYYMMDD'
     */
    public function setDate($data)
    {
        parent::set(self::DATE, $data);
    }
}