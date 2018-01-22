<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Account\Daily;

/**
 * Response to get account turnover summary by day & transaction type (Odoo replication).
 *
 * (Define getters explicitly to use with Swagger tool)
 */
class Response
    extends \Praxigento\Core\Data
{
    const ITEMS = 'items';

    /**
     * @return \Praxigento\Accounting\Service\Account\Asset\Get\Response\Item[]
     */
    public function getItems()
    {
        $result = parent::get(self::ITEMS);
        return $result;
    }

    /**
     * @param \Praxigento\Accounting\Service\Account\Asset\Get\Response\Item[] $data
     */
    public function setItems($data)
    {
        parent::set(self::ITEMS, $data);
    }

}