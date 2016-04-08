<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Service\Replicate\Request;

use Praxigento\Odoo\Api\Data\Def\Bundle;
use Praxigento\Odoo\Api\Data\IBundle;

class ProductSave extends \Praxigento\Core\Service\Base\Request
{
    /**
     * Get bundle of the products (one product is also allowed) to replicate data between Magento and Odoo.
     * @return  IBundle
     */
    public function getProductBundle()
    {
        $data = parent::getProductBundle();
        $result = new Bundle($data);
        return $result;
    }

    /**
     * Get bundle of the products (one product is also allowed) to replicate data between Magento and Odoo.
     *
     * @param IBundle $data
     */
    public function setProductBundle(IBundle $data)
    {
        parent::setProductBundle($data);
    }
}