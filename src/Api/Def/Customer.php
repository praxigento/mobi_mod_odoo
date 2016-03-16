<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Def;

use Praxigento\Odoo\Api\CustomerInterface;

class Customer implements CustomerInterface {

    public function read($id = null) {
        return;
    }
}