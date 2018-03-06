<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Web\Account\Pv\Add\Response;

class Data
    extends \Praxigento\Core\Data
{
    const OPERATION_ID = 'operationId';

    /**
     * @return int
     */
    public function getOperationId()
    {
        $result = parent::get(self::OPERATION_ID);
        return $result;
    }

    /**
     * @param int $data
     */
    public function setOperationId($data)
    {
        parent::set(self::OPERATION_ID, $data);
    }
}