<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity;

interface IOdooEntity
    extends \Praxigento\Core\Repo\IEntity
{
    /**
     * @param int $id Odoo ID of the entity registered in Magento.
     * @return \Praxigento\Odoo\Data\Entity\IOdooEntity|null
     */
    public function getByOdooId($id);

    /**
     * @param int $id Odoo ID of the entity registered in Magento.
     * @return int|null Magento ID for the corresponded Odoo entity.
     */
    public function getMageIdByOdooId($id);
}