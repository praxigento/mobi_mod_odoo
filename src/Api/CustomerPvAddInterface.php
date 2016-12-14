<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api;

/**
 * Web service command to add PV to customer account.
 *
 * @api
 */
interface CustomerPvAddInterface
{
    /**
     * Save product inventory data to Magento (push replication).
     *
     * @param \Praxigento\Odoo\Api\Def\Customer\Pv\Add\Request $data
     *
     * @return \Praxigento\Odoo\Api\Def\Customer\Pv\Add\Response
     */
    public function execute(\Praxigento\Odoo\Api\Def\Customer\Pv\Add\Request $data);
}