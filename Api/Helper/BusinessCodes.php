<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Helper;

/**
 * Define business codes (for shipping & payment methods) used in the concrete application.
 */
interface BusinessCodes
{
    /**
     * Get 'distributor' value by '1' value.
     *
     * @param int $groupId
     * @return string
     */
    public function getBusCodeForCustomerGroupById($groupId);

    /**
     * Get transaction type business code for operation type ID.
     *
     * @see https://confluence.prxgt.com/x/AwA2CQ
     *
     * @param int $typeId
     * @return string
     */
    public function getBusCodeForOperTypeId($typeId);

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
     * Convert business code for shipping methods to Magento code of the carrier.
     * See #getTitleForCarrier().
     *
     * @param string $businessCode for shipping method
     * @return string carrier's code
     */
    public function getMageCodeForCarrier($businessCode);

    /**
     * One transaction type can be mapped to multiple operation types.
     *
     * @param string $trnType business code for transaction type
     * @return array
     */
    public function getMageCodesForTransType($trnType);

    /**
     * Get '1' value by 'distributor' value.
     *
     * @param string $groupCode
     * @return int
     */
    public function getMageIdForCustomerGroupByCode($groupCode);

    /**
     * Convert business code for shipping methods to title of the tracking number.
     * See #getMagCodeForCarrier().
     *
     * @param string $businessCode for shipping method
     * @return string title for tracking number
     */
    public function getTitleForCarrier($businessCode);
}