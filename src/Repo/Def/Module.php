<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Def;

use Praxigento\Odoo\Repo\IModule;

class Module implements IModule
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    protected $_manObj;
    /** @var \Magento\Catalog\Api\CategoryRepositoryInterface */
    protected $_repoMageCat;

    /**
     * Module constructor.
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Catalog\Api\CategoryRepositoryInterface $repoMageCat
    ) {
        $this->_manObj = $manObj;
        $this->_repoMageCat = $repoMageCat;
    }

    public function getCategoryIdToPlaceNewProduct()
    {
        $cat = $this->_manObj->create(\Magento\Catalog\Api\Data\CategoryInterface::class);
        $this->_repoMageCat->get();
//        $this->_repoMageCat->get();
//        $this->_repoMageCat->save();
        return 1;
    }
}