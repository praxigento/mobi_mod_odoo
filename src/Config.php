<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo;

use Praxigento\Warehouse\Config as WrhsCfg;

class Config extends \Praxigento\Core\Config
{
    const ACL_CATALOG_LOTS = WrhsCfg::ACL_CATALOG_LOTS;
    const ACL_CATALOG_WAREHOUSES = WrhsCfg::ACL_CATALOG_WAREHOUSES;
    const MENU_CATALOG_LOTS = self::ACL_CATALOG_LOTS;
    const MENU_CATALOG_WAREHOUSES = self::ACL_CATALOG_WAREHOUSES;
    const MODULE = 'Praxigento_Odoo';
    /** Number of digits to round to for Odoo API percents values. */
    const ODOO_API_PERCENT_ROUND = 4;
    const ROUTE_NAME_ADMIN_CATALOG = WrhsCfg::ROUTE_NAME_ADMIN_CATALOG;
}