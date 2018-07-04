<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product;

use Praxigento\Odoo\Config as Cfg;

/**
 * Service level functions to replicate Odoo categories into Magento.
 */
class Category
{
    /** @var \Magento\Catalog\Api\CategoryRepositoryInterface */
    private $daoCat;
    /** @var \Magento\Catalog\Api\CategoryLinkRepositoryInterface */
    private $daoCatLink;
    /** @var \Praxigento\Core\Api\App\Repo\Generic */
    private $daoGeneric;
    /** @var \Praxigento\Odoo\Repo\Dao\Category */
    private $daoOdooCat;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $daoProd;
    /** @var \Magento\Catalog\Model\CategoryFactory */
    private $factCat;
    /** @var \Magento\Catalog\Model\CategoryProductLink */
    private $factCatProdLink;

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $factCat,
        \Magento\Catalog\Model\CategoryProductLinkFactory $factCatProdLink,
        \Magento\Catalog\Api\CategoryRepositoryInterface $daoCat,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface $daoCatLink,
        \Magento\Catalog\Api\ProductRepositoryInterface $daoProd,
        \Praxigento\Core\Api\App\Repo\Generic $daoGeneric,
        \Praxigento\Odoo\Repo\Dao\Category $daoOdooCat
    ) {
        $this->factCat = $factCat;
        $this->factCatProdLink = $factCatProdLink;
        $this->daoCat = $daoCat;
        $this->daoCatLink = $daoCatLink;
        $this->daoProd = $daoProd;
        $this->daoGeneric = $daoGeneric;
        $this->daoOdooCat = $daoOdooCat;
    }

    /**
     * Check all Odoo IDs for categories and create new Magento category if Odoo ID is not registered.
     *
     * @param int[] $cats
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function checkExistence($cats)
    {
        if (is_array($cats)) {
            foreach ($cats as $odooId) {
                /* get mageId by odooId from registry */
                $mageId = $this->daoOdooCat->getMageIdByOdooId($odooId);
                if (!$mageId) {
                    $mageId = $this->createMageCategory('Cat #' . $odooId);
                    $entity = new \Praxigento\Odoo\Repo\Data\Category();
                    $entity->setMageRef($mageId);
                    $entity->setOdooRef($odooId);
                    $this->daoOdooCat->create($entity);
                }
            }
        }
    }

    private function clearUrlRewrites($urlKey)
    {
        $byType = Cfg::E_URL_REWRITE_A_ENTITY_TYPE . '="product"';
        $where = "(`entity_type`) AND ()";
        $this->daoGeneric->deleteEntity(Cfg::ENTITY_MAGE_URL_REWRITE, $where);
    }

    /**
     * Create new Magento category with given $name.
     *
     * @param string $name
     * @return int ID of the created category
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function createMageCategory($name)
    {
        /** @var  $category \Magento\Catalog\Api\Data\CategoryInterface */
        $category = $this->factCat->create();
        $category->setName($name);
        /* MOBI-624 */
        $category->setIsActive(true);
        $saved = $this->daoCat->save($category);
        $result = $saved->getId();
        return $result;
    }

    /**
     * Replicate links between product & categories from Odoo to Magento.
     *
     * @param int $prodId Magento ID for the product
     * @param array $categories Odoo IDs of the categories.
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function exec($prodId, $categories)
    {
        $this->checkExistence($categories);
        $this->replicate($prodId, $categories);
    }

    /**
     * Replicate links between product & categories from Odoo to Magento.
     *
     * @param int $prodId Magento ID for the product
     * @param array $categories Odoo IDs of the categories.
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    private function replicate($prodId, $categories)
    {
        /* get current categories links for the product */
        $prod = $this->daoProd->getById($prodId);
        $urlKey = $prod->getUrlKey();
        $sku = $prod->getSku();
        $catsExist = $prod->getCategoryIds();
        $catsFound = [];
        if (is_array($categories)) {
            foreach ($categories as $catOdooId) {
                $catMageId = $this->daoOdooCat->getMageIdByOdooId($catOdooId);
                if (!in_array($catMageId, $catsExist)) {
                    /* remove URL redirects */
                    $this->clearUrlRewrites($urlKey);
                    /* create new product link if not exists */
                    /** @var \Magento\Catalog\Api\Data\CategoryProductLinkInterface $prodLink */
                    $prodLink = $this->factCatProdLink->create();
                    $prodLink->setCategoryId($catMageId);
                    $prodLink->setSku($sku);
                    $prodLink->setPosition(1);
                    $this->daoCatLink->save($prodLink);
                }
                /* register found link */
                $catsFound[] = $catMageId;
            }
        }
        /* get difference between exist & found */
        $diff = array_diff($catsExist, $catsFound);
        foreach ($diff as $catMageId) {
            $this->daoCatLink->deleteByIds($catMageId, $sku);
        }
    }
}