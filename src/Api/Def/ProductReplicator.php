<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Def;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Api;
use Praxigento\Odoo\Api\ProductReplicatorInterface;

class ProductReplicator implements ProductReplicatorInterface
{
    /** @var  \Praxigento\Odoo\Service\IReplicate */
    private $_callOdooReplicate;
    /** @var  ObjectManagerInterface */
    private $manObj;

    public function __construct(
        ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Service\IReplicate $callOdooReplicate
    ) {
        $this->manObj = $manObj;
        $this->_callOdooReplicate = $callOdooReplicate;
    }

    public function save(\Praxigento\Odoo\Data\Api\IBundle $data)
    {
        /** @var  $req \Praxigento\Odoo\Service\Replicate\Request\ProductSave */
        $req = $this->manObj->create(\Praxigento\Odoo\Service\Replicate\Request\ProductSave::class);
        $req->setProductBundle($data);
        $resp = $this->_callOdooReplicate->productSave($req);
        return;
    }

}