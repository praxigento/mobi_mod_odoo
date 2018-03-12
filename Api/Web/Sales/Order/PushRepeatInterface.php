<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Web\Sales\Order;

/**
 * Request sale orders data push (from Magento to Odoo).
 */
interface PushRepeatInterface
{

    /**
     * Command to request sale orders data push (from Magento to Odoo).
     *
     * @param \Praxigento\Odoo\Api\Web\Sales\Order\PushRepeat\Request $request
     * @return \Praxigento\Odoo\Api\Web\Sales\Order\PushRepeat\Response
     *
     * Magento 2 WebAPI requires full names in documentation (aliases are not allowed).
     */
    public function exec($request);
}