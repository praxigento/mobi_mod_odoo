<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sale;

use Praxigento\Odoo\Config as Cfg;


class Orders
    extends \Praxigento\Core\Service\Base\Call
    implements \Praxigento\Odoo\Service\Replicate\Sale\IOrders
{
    public function exec(\Praxigento\Odoo\Service\Replicate\Sale\Orders\Request $req)
    {
        $result = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Response();
        $q = Cfg::E_CATINV_STOCK_ITEM_A_ITEM_ID;
        return $result;
    }


}