<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Config;


interface IAuthentication extends IConnection
{
    /**
     * User name to authenticate connection ('magento').
     *
     * @return string
     */
    public function getUserName();

    /**
     * User password to authenticate connection ('OLxj3RikCN59pUb8nQRR').
     *
     * @return string
     */
    public function getUserPassword();
}