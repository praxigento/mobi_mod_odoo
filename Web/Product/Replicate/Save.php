<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Web\Product\Replicate;

use Praxigento\Odoo\Api\Web\Product\Replicate\Save\Request as ARequest;
use Praxigento\Odoo\Api\Web\Product\Replicate\Save\Response as AResponse;

class Save
    implements \Praxigento\Odoo\Api\Web\Product\Replicate\SaveInterface
{
    /** @var \Praxigento\Odoo\Service\Replicate\Product\Save */
    private $servSave;
    /** @var \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    private $manTrans;

    public function __construct(
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\Odoo\Service\Replicate\Product\Save $servSave
    ) {
        $this->manTrans = $manTrans;
        $this->servSave = $servSave;
    }

    public function exec($request)
    {
        assert($request instanceof ARequest);
        /** define local working data */
        $data = $request->getData();
        $respResult = new \Praxigento\Core\Api\App\Web\Response\Result();

        /** perform processing */
        $def = $this->manTrans->begin();
        try {
            $req = new \Praxigento\Odoo\Service\Replicate\Product\Save\Request();
            $req->setInventory($data);
            $this->servSave->exec($req);
            $this->manTrans->commit($def);
            $respResult->setCode(AResponse::CODE_SUCCESS);
        } finally {
            /* rollback uncommitted transactions on exception */
            $this->manTrans->end($def);
        }

        /** compose result */
        $result = new AResponse();
        $result->setResult($respResult);
        return $result;
    }

}