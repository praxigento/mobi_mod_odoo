<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Web\Account\Transaction\A;


class DataSource
{
    /** @var \Praxigento\Downline\Repo\Query\Account\Trans\Get  */
    private $qTransGet;

    public function __construct(
        \Praxigento\Downline\Repo\Query\Account\Trans\Get $qTransGet
    ) {
        $this->qTransGet = $qTransGet;
    }

}