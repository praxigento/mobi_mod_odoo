<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Entity\Registry;

/**
 * Odoo requests registry to prevent double processing.
 */
class Request
    extends \Praxigento\Core\Data\Entity\Base
{
    const ENTITY_NAME = 'prxgt_odoo_reg_request';
}