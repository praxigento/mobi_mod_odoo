<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat;

/**
 * Report with sales orders replication results.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 */
class Report
    extends \Flancer32\Lib\DataObject
{

    /**
     * Report entries with results for each order.
     *
     * @return \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report\Entry[]
     */
    public function getEntries()
    {
        $result = parent::getEntries();
        return $result;
    }

    /**
     * @param \Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report\Entry[] $data
     */
    public function setEntries($data)
    {
        parent::setEntries($data);
    }
}