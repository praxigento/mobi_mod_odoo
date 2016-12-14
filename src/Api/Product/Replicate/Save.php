<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Product\Replicate;

class Save
    implements \Praxigento\Odoo\Api\Product\Replicate\SaveInterface
{
    /** @var  \Praxigento\Odoo\Service\IReplicate */
    protected $callOdooReplicate;

    public function __construct(
        \Praxigento\Odoo\Service\IReplicate $callOdooReplicate
    ) {
        $this->callOdooReplicate = $callOdooReplicate;
    }

    public function execute(\Praxigento\Odoo\Data\Odoo\Inventory $data)
    {
        $req = new \Praxigento\Odoo\Service\Replicate\Request\ProductSave();
        $req->setProductBundle($data);
        $resp = $this->callOdooReplicate->productSave($req);
        $result = $resp->isSucceed();
        return $result;
    }

}