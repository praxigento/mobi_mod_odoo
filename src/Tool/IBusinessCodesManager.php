<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Tool;

/**
 * Define business codes (for shipping & payment methods) used in the concrete application.
 */
interface IBusinessCodesManager
{
    /**
     * Extract business code for payment method.
     *
     * @param \Magento\Sales\Api\Data\OrderPaymentInterface $payment
     * @return string
     */
    public function getBusCodeForPaymentMethod(\Magento\Sales\Api\Data\OrderPaymentInterface $payment);

    /**
     * Define business code for shipping method is used in sale order.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return string
     */
    public function getBusCodeForShippingMethod(\Magento\Sales\Api\Data\OrderInterface $order);

    /**
     * Get 'distributor' value by '1' value.
     *
     * @param int $groupId
     * @return string
     */
    public function getBusCodeForCustomerGroupById($groupId);

    /**
     * Convert business code for shipping methods to Magento code of the carrier.
     * See #getTitleForCarrier().
     *
     * @param string $businessCode for shipping method
     * @return string carrier's code
     */
    public function getMagCodeForCarrier($businessCode);

    /**
     * Convert business code for shipping methods to title of the tracking number.
     * See #getMagCodeForCarrier().
     *
     * @param string $businessCode for shipping method
     * @return string title for tracking number
     */
    public function getTitleForCarrier($businessCode);
}