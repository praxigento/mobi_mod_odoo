<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Customer\Pv;

/**
 * Add PV to customer account.
 *
 * @api
 */
interface AddInterface
{
    /**
     * Command to add PV to customer account.
     *
     * @param \Praxigento\Odoo\Api\Data\Customer\Pv\Add\Request $data
     *
     * @return \Praxigento\Odoo\Api\Data\Customer\Pv\Add\Response
     */
    public function execute(\Praxigento\Odoo\Api\Data\Customer\Pv\Add\Request $data);
}