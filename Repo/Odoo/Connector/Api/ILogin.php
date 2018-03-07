<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Api;


interface ILogin
{
    /**
     * Authenticate user to perform requests to Odoo REST API.
     *
     * @return int session ID for user that is logged in into Odoo.
     */
    public function getSessionId();

    /**
     * Authenticate user to perform requests to Odoo XML RPC API.
     *
     * @return int ID for user that is logged in into Odoo.
     */
    public function getUserId();
}