<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Data\SaleOrder;

/**
 * Response for "/sale_order" POST operation.
 *
 * @method int getIdMage()
 * @method void setIdMage(int $data)
 * @method int getIdOdoo()
 * @method void setIdOdoo(int $data)
 * @method string getStatus()
 * @method void setStatus(string $data)
 * @method \Praxigento\Odoo\Repo\Odoo\Data\Invoice[] getInvoices()
 * @method void setInvoices(\Praxigento\Odoo\Repo\Odoo\Data\Invoice[] $data)
 * @method \Praxigento\Odoo\Repo\Odoo\Data\Shipment[] getShipments()
 * @method void setShipments(\Praxigento\Odoo\Repo\Odoo\Data\Shipment[] $data)
 */
class Response
    extends \Praxigento\Core\Data
{

}