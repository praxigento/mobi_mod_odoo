<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator\Product;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategoryProductLinkInterface;

class Category
{

    /** @var   \Magento\Framework\ObjectManagerInterface */
    protected $manObj;
    /** @var  \Magento\Catalog\Api\CategoryLinkRepositoryInterface */
    protected $repoCatLink;
    /** @var   \Magento\Catalog\Api\CategoryRepositoryInterface */
    protected $repoCategory;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $repoProd;
    /** @var  \Praxigento\Odoo\Repo\IRegistry */
    protected $repoRegistry;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface\Proxy $repoCatLink,
        \Magento\Catalog\Api\CategoryRepositoryInterface $repoCat,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Praxigento\Odoo\Repo\IRegistry $repoRegistry
    ) {
        $this->manObj = $manObj;
        $this->repoCatLink = $repoCatLink;
        $this->repoCategory = $repoCat;
        $this->repoProd = $repoProd;
        $this->repoRegistry = $repoRegistry;
    }

    /**
     * Check all Odoo IDs for categories and create new Magento category if Odoo ID is not registered.
     *
     * @param int[] $cats
     */
    public function checkCategoriesExistence($cats)
    {
        if (is_array($cats)) {
            foreach ($cats as $odooId) {
                /* get mageId by odooId from registry */
                $mageId = $this->repoRegistry->getCategoryMageIdByOdooId($odooId);
                if (!$mageId) {
                    $mageId = $this->createMageCategory('Cat #' . $odooId);
                    $this->repoRegistry->registerCategory($mageId, $odooId);
                }
            }
        }
    }

    /**
     * Create new Magento category with given $name.
     *
     * @param string $name
     * @return int ID of the created category
     */
    public function createMageCategory($name)
    {
        /** @var  $category CategoryInterface */
        $category = $this->manObj->create(CategoryInterface::class);
        $category->setName($name);
        $category->setIsActive(false);
        $saved = $this->repoCategory->save($category);
        $result = $saved->getId();
        return $result;
    }

    /**
     * @param int $prodId Magento ID for the product
     * @param array $categories Odoo IDs of the categories.
     */
    public function replicateCategories($prodId, $categories)
    {
        /* get current categories links for the product */
        $prod = $this->repoProd->getById($prodId);
        $sku = $prod->getSku();
        $catsExist = $prod->getCategoryIds();
        $catsFound = [];
        if (is_array($categories)) {
            foreach ($categories as $catOdooId) {
                $catMageId = $this->repoRegistry->getCategoryMageIdByOdooId($catOdooId);
                if (!in_array($catMageId, $catsExist)) {
                    /* create new product link if not exists */
                    /** @var CategoryProductLinkInterface $prodLink */
                    $prodLink = $this->manObj->create(CategoryProductLinkInterface::class);
                    $prodLink->setCategoryId($catMageId);
                    $prodLink->setSku($sku);
                    $prodLink->setPosition(1);
                    $this->repoCatLink->save($prodLink);
                }
                /* register found link */
                $catsFound[] = $catMageId;
            }
        }
        /* get difference between exist & found */
        $diff = array_diff($catsExist, $catsFound);
        foreach ($diff as $catMageId) {
            $this->repoCatLink->deleteByIds($catMageId, $sku);
        }
    }
}