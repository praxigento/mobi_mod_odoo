<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account\Daily;

/**
 * Request to get account turnover summary by day & transaction type (Odoo replication).
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 *
 */
class Request
    extends \Praxigento\Core\Api\App\Web\Request
{
    /**
     * @return \Praxigento\Odoo\Api\Web\Account\Daily\Request\Data
     */
    public function getData() {
        $result = parent::get(self::DATA);
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Daily\Request\Data $data
     */
    public function setData($data) {
        parent::set(self::DATA, $data);
    }

}