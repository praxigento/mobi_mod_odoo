<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Data\SaleOrder\Shipment;

use Flancer32\Lib\DataObject;

/**
 * Shipment tracking information for sale orders.
 *
 * @method int getSaleOrderIdMage() Magento ID for sale order.
 * @method void setSaleOrderIdMage(int $data)
 * @method \Praxigento\Odoo\Data\Odoo\Shipment getShipment()
 * @method void setShipment(\Praxigento\Odoo\Data\Odoo\Shipment $data)
 */
class Tracking
    extends DataObject
{

}