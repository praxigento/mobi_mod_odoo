<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Dao;

/**
 * Common interface for entities related to Odoo replication.
 */
interface IOdooDao
    extends \Praxigento\Core\Api\App\Repo\Dao
{
    /**
     * Get entity by Odoo ID.
     *
     * @param int $id Odoo ID of the entity registered in Magento.
     * @return \Praxigento\Odoo\Repo\Data\IOdooEntity|null
     */
    public function getByOdooId($id);

    /**
     * @param int $id Odoo ID of the entity registered in Magento.
     * @return int|null Magento ID for the corresponded Odoo entity.
     */
    public function getMageIdByOdooId($id);

    /**
     * @param int $id Magento ID for the corresponded Odoo entity.
     * @return int Odoo ID of the entity registered in Magento.
     */
    public function getOdooIdByMageId($id);
}