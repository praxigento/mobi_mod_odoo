<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Web\Customer\Pv\Add\Response;

/**
 * Response for add PV to customer balance.
 *
 * (Define getters explicitly to use with Swagger tool)
 *
 * @method void setOdooRef(string $data)
 * @method void setOperationId(int $data)
 * @method void setTransactionId(int $data)
 */
class Data
    extends \Praxigento\Core\Data
{
    /**
     * Odoo reference of the original request.
     *
     * @return string
     */
    public function getOdooRef()
    {
        $result = parent::getOdooRef();
        return $result;
    }

    /**
     * Magento ID of the created operation.
     *
     * @return int
     */
    public function getOperationId()
    {
        $result = parent::getOperationId();
        return $result;
    }

    /**
     * Magento ID of the created transaction.
     *
     * @return int
     */
    public function getTransactionId()
    {
        $result = parent::getTransactionId();
        return $result;
    }
}