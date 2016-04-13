<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo;

class Logger extends \Praxigento\Logging\Logger
{
    const DEFAULT_LOGGER_NAME = 'odoo';

    public function __construct($configFile, $loggerName)
    {
        parent::__construct($configFile, $loggerName);
    }

}