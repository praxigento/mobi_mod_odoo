<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Odoo\Web\Customer\Get;

use Praxigento\Core\Api\App\Web\Response\Result as WResult;
use Praxigento\Odoo\Api\Web\Customer\Get\All\Request as WRequest;
use Praxigento\Odoo\Api\Web\Customer\Get\All\Response as WResponse;
use Praxigento\Odoo\Web\Customer\Get\A\Repo\Query\GetAll as QGetAll;

/**
 * Get list of all customers.
 */
class All
    implements \Praxigento\Odoo\Api\Web\Customer\Get\AllInterface
{
    /** @var  \Praxigento\Odoo\Api\Helper\BusinessCodes */
    private $manBusinessCodes;
    /** @var \Praxigento\Odoo\Web\Customer\Get\A\Repo\Query\GetAll */
    private $qGetAll;

    public function __construct(
        \Praxigento\Odoo\Api\Helper\BusinessCodes $manBusinessCodes,
        \Praxigento\Odoo\Web\Customer\Get\A\Repo\Query\GetAll $qGetAll
    ) {
        $this->manBusinessCodes = $manBusinessCodes;
        $this->qGetAll = $qGetAll;
    }

    public function exec($request)
    {
        assert($request instanceof WRequest);
        /** define local working data */
        $respRes = new WResult();
        $respData = [];

        /** perform processing */
        $query = $this->qGetAll->build();
        $conn = $query->getConnection();
        $rs = $conn->fetchAll($query);
        foreach ($rs as $one) {
            $idMlm = $one[QGetAll::A_ID_MLM];
            /* skip company representative customer (w/o MLM ID)*/
            if (is_null($idMlm)) {
                continue;
            }
            $idMage = $one[QGetAll::A_ID_MAGE];
            $groupId = $one[QGetAll::A_ID_GROUP];
            $name = $one[QGetAll::A_NAME];
            $groupCode = $this->manBusinessCodes->getBusCodeForCustomerGroupById($groupId);
            $entry = new \Praxigento\Odoo\Repo\Odoo\Data\SaleOrder\Customer();
            $entry->setIdMage($idMage);
            $entry->setIdMlm($idMlm);
            $entry->setGroupCode($groupCode);
            $entry->setName($name);
            $respData[] = $entry;
        }
        /** compose result */
        $result = new WResponse();
        $respRes->setCode(WResponse::CODE_SUCCESS);
        $result->setResult($respRes);
        $result->setData($respData);
        return $result;
    }
}