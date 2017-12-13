<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Fw\Logger;

/**
 * Odoo interconnection logger. Use this logger to log Odoo related messages.
 *
 * This logger is used the following default configuration parameters:
 *  - Monolog Cascade YAML config: 'var/log/logging.yaml'
 *  - YAML config logger name:     'odoo'
 */
class Odoo
    extends \Psr\Log\LoggerInterface
{
    /** Defalt name of the logger from configuration */
    const LOGGER_NAME = 'odoo';
}