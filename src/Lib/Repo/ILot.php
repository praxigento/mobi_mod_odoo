<?php
/**
 * Interface for Repository that operates with "\Praxigento\Odoo\Data\Agg\Lot" (aggregation of Warehouse Lot with data from Odoo Lot).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Lib\Repo;

use Praxigento\Odoo\Data\Agg\Lot as AggLot;

interface ILot
{
    /**
     * Check existence of the 'Lot' aggregate in Magento and register new 'Lot' if required.
     * @param AggLot $data data to check and register
     * @return AggLot|null data from Magento (new or existed)
     */
    public function checkExistence(AggLot $data);

    /**
     * @param int $id Magento ID of the Lot.
     * @return AggLot|null
     */
    public function getById($id);

    /**
     * @param int $id Odoo ID of the Lot registered in Magento.
     * @return AggLot|null
     */
    public function getByOdooId($id);
}