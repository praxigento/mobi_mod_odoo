<?php
/**
 * Interface for Repository that operates with "\Praxigento\Odoo\Data\Agg\Lot" (aggregation of Warehouse Lot with data from Odoo Lot).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Agg;

use Praxigento\Odoo\Data\Agg\Lot as AggLot;

interface ILot extends \Praxigento\Core\Repo\IBaseCrud
{
    /**#@+
     *  Aliases for tables in DB.
     */
    const AS_ODOO = 'pol';
    const AS_WRHS = 'pwl';
    /**#@- */

    /**
     * @param AggLot $data
     * @return AggLot
     */
    public function create($data);

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