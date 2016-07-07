<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Tool;

/**
 * Define business codes (for shipping & payment methods) used in the concrete application.
 *
 * TODO: MOBI APP IMPL (interface should be implemented on app level).
 */
interface IBusinessCodesManager
{
    /**
     * Extract business code for payment method.
     *
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @return string
     */
    public function getPaymentMethodCode(\Magento\Sales\Api\Data\OrderPaymentInterface $payment);

    /**
     * Define business code for shipping method is used in sale order.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string
     */
    public function getShippingMethodCode(\Magento\Sales\Api\Data\OrderInterface $order);
}