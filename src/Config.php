<?php
/**
 * Module's configuration (hard-coded).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo;

use Praxigento\Wallet\Config as WalletCfg;
use Praxigento\Warehouse\Config as WrhsCfg;

class Config extends \Praxigento\Core\Config
{
    const ACL_CATALOG_LOTS = WrhsCfg::ACL_CATALOG_LOTS;
    const ACL_CATALOG_WAREHOUSES = WrhsCfg::ACL_CATALOG_WAREHOUSES;

    const CODE_TYPE_ASSET_WALLET_ACTIVE = WalletCfg::CODE_TYPE_ASSET_WALLET;
    const MENU_CATALOG_LOTS = self::ACL_CATALOG_LOTS;
    const MENU_CATALOG_WAREHOUSES = self::ACL_CATALOG_WAREHOUSES;

    const MODULE = 'Praxigento_Odoo';

    /** ID & code for the virtual lot that is related to the Odoo products w/o lots */
    const NULL_LOT_CODE = 'NULL_LOT';
    const NULL_LOT_ID = 0;

    /** Number of digits to round to for Odoo API percents values. */
    const ODOO_API_PERCENT_ROUND = 4;

    const ROUTE_NAME_ADMIN_CATALOG = WrhsCfg::ROUTE_NAME_ADMIN_CATALOG;


}