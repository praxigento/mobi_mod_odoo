<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Entity;

interface ISaleOrder
    extends \Praxigento\Odoo\Repo\Entity\IOdooEntity
{
    /**
     * Get Magento IDs to save new orders into Odoo.
     *
     * @return int[] Magento IDs of the orders to be replicated.
     */
    public function getIdsToSaveToOdoo();
}