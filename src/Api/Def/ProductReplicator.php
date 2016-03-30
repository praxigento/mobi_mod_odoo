<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Api\Def;

use Praxigento\Core\Lib\Context\IObjectManager;
use Praxigento\Core\Lib\Context\ObjectManagerFactory;
use Praxigento\Odoo\Api\Data;
use Praxigento\Odoo\Api\ProductReplicatorInterface;

class ProductReplicator implements ProductReplicatorInterface
{
    /** @var  \Praxigento\Odoo\Lib\Service\IReplicate */
    private $_callOdooReplicate;
    /** @var  IObjectManager */
    private $_obm;

    public function __construct(
        ObjectManagerFactory $omf,
        \Praxigento\Odoo\Lib\Service\IReplicate $callOdooReplicate
    ) {
        $this->_obm = $omf->create();
        $this->_callOdooReplicate = $callOdooReplicate;
    }

    public function save(\Praxigento\Odoo\Api\Data\IBundle $data)
    {
        /** @var  $req \Praxigento\Odoo\Lib\Service\Replicate\Request\ProductSave */
        $req = $this->_obm->create(\Praxigento\Odoo\Lib\Service\Replicate\Request\ProductSave::class);
        $req->setProductBundle($data);
        $resp = $this->_callOdooReplicate->productSave($req);
        return;
    }

}