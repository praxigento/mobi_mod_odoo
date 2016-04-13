<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service;


interface IReplicate
{
    /**
     * @param Replicate\Request\ProductSave $req
     * @return Replicate\Response\ProductSave
     */
    public function productSave(Replicate\Request\ProductSave $req);

    /**
     * @param Replicate\Request\ProductsFromOdoo $req
     * @return Replicate\Response\ProductsFromOdoo
     */
    public function productsFromOdoo(Replicate\Request\ProductsFromOdoo $req);
}