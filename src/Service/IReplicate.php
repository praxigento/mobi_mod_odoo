<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service;


interface IReplicate
{
    /**
     * Save products bundle ('push' replication).
     *
     * @param Replicate\Request\ProductSave $req
     * @return Replicate\Response\ProductSave
     */
    public function productSave(Replicate\Request\ProductSave $req);

    /**
     * Get products bundle from Odoo and save it ('pull' replication).
     *
     * @param Replicate\Request\ProductsFromOdoo $req
     * @return Replicate\Response\ProductsFromOdoo
     */
    public function productsFromOdoo(Replicate\Request\ProductsFromOdoo $req);
    
}