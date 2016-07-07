<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Def;

/**
 * Implementation of the service to push product data from Odoo to Magento.
 */
class ProductReplicator
    implements \Praxigento\Odoo\Api\ProductReplicatorInterface
{
    /** @var  \Praxigento\Odoo\Service\IReplicate */
    private $_callOdooReplicate;
    /** @var  \Magento\Framework\ObjectManagerInterface */
    private $manObj;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Odoo\Service\IReplicate $callOdooReplicate
    ) {
        $this->manObj = $manObj;
        $this->_callOdooReplicate = $callOdooReplicate;
    }

    /** @inheritdoc */
    public function save(\Praxigento\Odoo\Data\Odoo\Inventory $data)
    {
        /** @var  $req \Praxigento\Odoo\Service\Replicate\Request\ProductSave */
        $req = $this->manObj->create(\Praxigento\Odoo\Service\Replicate\Request\ProductSave::class);
        $req->setProductBundle($data);
        $resp = $this->_callOdooReplicate->productSave($req);
        $result = $resp->isSucceed();
        return $result;
    }

}