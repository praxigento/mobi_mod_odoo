<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Def;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Api\Data;
use Praxigento\Odoo\Api\ProductReplicatorInterface;

class ProductReplicator implements ProductReplicatorInterface
{
    /** @var  \Praxigento\Odoo\Lib\Service\IReplicate */
    private $_callOdooReplicate;
    /** @var  ObjectManagerInterface */
    private $_obm;

    public function __construct(
        ObjectManagerInterface $obm,
        \Praxigento\Odoo\Lib\Service\IReplicate $callOdooReplicate
    ) {
        $this->_obm = $obm;
        $this->_callOdooReplicate = $callOdooReplicate;
    }

    public function save(\Praxigento\Odoo\Lib\Data\Dict\IBundle $data)
    {
        /** @var  $req \Praxigento\Odoo\Lib\Service\IReplicate */
        $req = $this->_obm->create(\Praxigento\Odoo\Lib\Service\Replicate\Request\ProductSave::class);
        $req->setProductBundle($data);
        $resp = $this->_callOdooReplicate->productSave($req);
        return;
    }

}