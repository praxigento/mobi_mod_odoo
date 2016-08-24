<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Data\SaleOrder\PushRepeat\Report;

/**
 * Replication results for one order.
 *
 * @method int getIdMage()
 * @method void setIdMage(int $data)
 * @method string getNumber()
 * @method void setNumber(string $data)
 * @method bool getIsSucceed() 'true' - order is successfully saved into Odoo.
 * @method void setIsSucceed(bool $data)
 * @method string|null getDebug() debug stacktrace from Odoo.
 * @method void setDebug(string $data)
 * @method string|null getErrorName() error name from Odoo.
 * @method void setErrorName(string $data)
 */
class Entry
    extends \Flancer32\Lib\DataObject
{

}