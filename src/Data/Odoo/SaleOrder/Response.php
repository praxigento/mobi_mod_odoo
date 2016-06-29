<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Data\Odoo\SaleOrder;

use Praxigento\Odoo\Data\Odoo\Invoice;
use Praxigento\Odoo\Data\Odoo\Shipment;

/**
 * Response for "/sale_order" POST operation.
 *
 * @method int getIdMage()
 * @method void setIdMage(int $data)
 * @method string getStatus()
 * @method void setStatus(string $data)
 * @method Invoice[] getInvoices()
 * @method void setInvoices(Invoice[] $data)
 * @method Shipment[] getShipments()
 * @method void setShipments(Shipment[] $data)
 */
class Response extends \Flancer32\Lib\DataObject
{
   
}