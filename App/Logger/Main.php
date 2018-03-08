<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\App\Logger;


class Main
    extends \Praxigento\Core\App\Logger\Main
    implements \Praxigento\Odoo\Api\App\Logger\Main
{
    const FILENAME = 'mobi.odoo.log';
    const NAME = 'ODOO';
}