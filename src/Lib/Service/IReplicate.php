<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Lib\Service;


interface IReplicate
{
    /**
     * @param Replicate\Request\ProductSave $req
     * @return Replicate\Response\ProductSave
     */
    public function productSave(Replicate\Request\ProductSave $req);
}