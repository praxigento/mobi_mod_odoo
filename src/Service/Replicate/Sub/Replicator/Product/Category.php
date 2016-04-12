<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

use Magento\Catalog\Api\CategoryLinkRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity\Category as EntityCategory;
use Praxigento\Odoo\Repo\IModule;

class Category
{

    /** @var  CategoryLinkRepositoryInterface */
    protected $_mageRepoCatLink;
    /** @var ProductRepositoryInterface */
    protected $_mageRepoProd;
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var  IModule */
    protected $_repoMod;

    public function __construct(
        ObjectManagerInterface $manObj,
        ProductRepositoryInterface $mageRepoProd,
        CategoryLinkRepositoryInterface $mageRepoCatLink,
        IModule $repoMod
    ) {
        $this->_manObj = $manObj;
        $this->_mageRepoProd = $mageRepoProd;
        $this->_mageRepoCatLink = $mageRepoCatLink;
        $this->_repoMod = $repoMod;
    }

    /**
     * @param int $prodId Magento ID for the product
     * @param array $categories Odoo IDs of the categories.
     */
    public function replicateCategories($prodId, $categories)
    {
        /* get current categories links for the product */
        $prod = $this->_mageRepoProd->getById($prodId);
        $sku = $prod->getSku();
        $catsExist = $prod->getCategoryIds();
        $catsFound = [];
        foreach ($categories as $catOdooId) {
            $catMageId = $this->_repoMod->getMageIdByOdooId(EntityCategory::ENTITY_NAME, $catOdooId);
            if (!in_array($catMageId, $catsExist)) {
                /* create new product link if not exists */
                /** @var CategoryProductLinkInterface $prodLink */
                $prodLink = $this->_manObj->create(CategoryProductLinkInterface::class);
                $prodLink->setCategoryId($catMageId);
                $prodLink->setSku($sku);
                $prodLink->setPosition(1);
                $this->_mageRepoCatLink->save($prodLink);
            }
            /* register found link */
            $catsFound[] = $catMageId;
        }
        /* get difference between exist & found */
        $diff = array_diff($catsExist, $catsFound);
        foreach ($diff as $catMageId) {
            $this->_mageRepoCatLink->deleteByIds($catMageId, $sku);
        }
    }
}