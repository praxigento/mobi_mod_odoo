<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account;

/**
 * Get account turnover summary by day & transaction type (Odoo replication).
 */
interface DailyInterface
{
    /**
     * @param \Praxigento\Odoo\Api\Web\Account\Daily\Request $request
     * @return \Praxigento\Odoo\Api\Web\Account\Daily\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}