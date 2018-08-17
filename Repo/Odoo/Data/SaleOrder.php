<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Data;

/**
 * @method int getIdMage()
 * @method void setIdMage(int $data)
 * @method int getWarehouseIdOdoo()
 * @method void setWarehouseIdOdoo(int $data)
 * @method string getNumber()
 * @method void setNumber(string $data)
 * @method string getDatePaid()
 * @method void setDatePaid(string $data)
 * @method \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Customer getCustomer()
 * @method void setCustomer(\Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Customer $data)
 * @method \Praxigento\Odoo\Repo\Odoo\Data\Contact getAddrBilling()
 * @method void setAddrBilling(\Praxigento\Odoo\Repo\Odoo\Data\Contact $data)
 * @method \Praxigento\Odoo\Repo\Odoo\Data\Contact getAddrShipping()
 * @method void setAddrShipping(\Praxigento\Odoo\Repo\Odoo\Data\Contact $data)
 * @method float getPvTotal()
 * @method void setPvTotal(float $data)
 * @method \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price getPrice()
 * @method void setPrice(\Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Price $data)
 * @method \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line[] getLines()
 * @method void setLines(\Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Line [] $data)
 * @method \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping getShipping()
 * @method void setShipping(\Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Shipping $data)
 * @method \Praxigento\Odoo\Repo\Odoo\Data\Payment[] getPayments()
 * @method void setPayments(\Praxigento\Odoo\Repo\Odoo\Data\Payment [] $data)
 */
class SaleOrder
    extends \Praxigento\Core\Data
{

}