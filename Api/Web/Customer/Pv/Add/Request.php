<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Web\Customer\Pv\Add;

/**
 * Request to add PV to customer balance.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 * @method void setCustomerMlmId(string $data)
 * @method void setPv(float $data)
 * @method void setOdooReference(string $data)
 * @method void setDateApplied(string $data)
 *
 */
class Request
    extends \Praxigento\Core\Data
{
    /**
     * MLM ID for customer.
     *
     * @return string
     */
    public function getCustomerMlmId()
    {
        $result = parent::getCustomerMlmId();
        return $result;
    }

    /**
     * Application date for transaction (ISO-8601 compatible, UTC or with timezone, now if missed).
     *
     * @return string|null
     */
    public function getDateApplied()
    {
        $result = parent::getDateApplied();
        return $result;
    }

    /**
     * Reference to the corresponded operation in Odoo (to prevent doubling).
     *
     * @return string
     */
    public function getOdooRef()
    {
        $result = parent::getOdooRef();
        return $result;
    }

    /**
     * Amount of the PV to add to customer balance.
     *
     * @return float
     */
    public function getPv()
    {
        $result = parent::getPv();
        return $result;
    }
}