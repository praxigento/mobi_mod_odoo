<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Config;


interface IConnection
{
    /**
     * Base URI to connect to Odoo API ('http://host.domain.org:8122').
     *
     * @return string
     */
    public function getBaseUri();

    /**
     * Database name to connect to ('oe_odoo9_api').
     *
     * @return string
     */
    public function getDbName();
}