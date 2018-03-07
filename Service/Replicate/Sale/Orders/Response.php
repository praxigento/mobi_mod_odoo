<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale\Orders;

class Response
    extends \Praxigento\Core\App\Service\Base\Response
{
    /**
     * Report entries with results for each order.
     *
     * @return \Praxigento\Odoo\Service\Replicate\Sale\Orders\Response\Entry[]
     */
    public function getEntries()
    {
        $result = parent::getEntries();
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Service\Replicate\Sale\Orders\Response\Entry[] $data
     */
    public function setEntries($data)
    {
        parent::setEntries($data);
    }
}