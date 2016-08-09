<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Odoo\SaleOrder;

/**
 * Response for "/sale_order" POST operation.
 *
 * @method int getIdMage()
 * @method void setIdMage(int $data)
 * @method int getIdOdoo()
 * @method void setIdOdoo(int $data)
 * @method string getStatus()
 * @method void setStatus(string $data)
 * @method \Praxigento\Odoo\Data\Odoo\Invoice[] getInvoices()
 * @method void setInvoices(\Praxigento\Odoo\Data\Odoo\Invoice[] $data)
 * @method \Praxigento\Odoo\Data\Odoo\Shipment[] getShipments()
 * @method void setShipments(\Praxigento\Odoo\Data\Odoo\Shipment[] $data)
 */
class Response extends \Flancer32\Lib\DataObject
{

}