<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Customer\Wallet\Balance\Response;

/**
 * Get balance for customer wallet.
 *
 * (Define getters explicitly to use with Swagger tool)
 * (Define setters explicitly to use with Magento JSON2PHP conversion tool)
 */
class Data
    extends \Praxigento\Core\Data
{
    const ODOO_REF = 'odooRef';
    const OPERATION_ID = 'operationId';

    /**
     * Odoo reference of the original request.
     *
     * @return string
     */
    public function getOdooRef()
    {
        $result = parent::get(self::ODOO_REF);
        return $result;
    }

    /**
     * Magento ID of the created operation.
     *
     * @return int
     */
    public function getOperationId()
    {
        $result = parent::get(self::OPERATION_ID);
        return $result;
    }

    /** @param string */
    public function setOdooRef($data)
    {
        parent::set(self::ODOO_REF, $data);
    }

    /** @param int */
    public function setOperationId($data)
    {
        parent::set(self::OPERATION_ID, $data);
    }

}