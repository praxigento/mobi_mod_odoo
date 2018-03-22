<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Product\Save\Own\Product;

/**
 * Service level functions to replicate Odoo categories into Magento.
 */
class Category
{
    /** @var \Magento\Catalog\Model\CategoryFactory */
    private $factCat;
    /** @var \Magento\Catalog\Model\CategoryProductLink */
    private $factCatProdLink;
    /** @var \Magento\Catalog\Api\CategoryRepositoryInterface */
    private $repoCat;
    /** @var \Magento\Catalog\Api\CategoryLinkRepositoryInterface */
    private $repoCatLink;
    /** @var \Praxigento\Odoo\Repo\Dao\Category */
    private $repoOdooCat;
    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    private $repoProd;

    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $factCat,
        \Magento\Catalog\Model\CategoryProductLinkFactory $factCatProdLink,
        \Magento\Catalog\Api\CategoryRepositoryInterface $repoCat,
        \Magento\Catalog\Api\CategoryLinkRepositoryInterface $repoCatLink,
        \Magento\Catalog\Api\ProductRepositoryInterface $repoProd,
        \Praxigento\Odoo\Repo\Dao\Category $repoOdooCat
    ) {
        $this->factCat = $factCat;
        $this->factCatProdLink = $factCatProdLink;
        $this->repoCat = $repoCat;
        $this->repoCatLink = $repoCatLink;
        $this->repoProd = $repoProd;
        $this->repoOdooCat = $repoOdooCat;
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
                $mageId = $this->repoOdooCat->getMageIdByOdooId($odooId);
                if (!$mageId) {
                    $mageId = $this->createMageCategory('Cat #' . $odooId);
                    $entity = new \Praxigento\Odoo\Repo\Data\Category();
                    $entity->setMageRef($mageId);
                    $entity->setOdooRef($odooId);
                    $this->repoOdooCat->create($entity);
                }
            }
        }
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
        $saved = $this->repoCat->save($category);
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
        $prod = $this->repoProd->getById($prodId);
        $sku = $prod->getSku();
        $catsExist = $prod->getCategoryIds();
        $catsFound = [];
        if (is_array($categories)) {
            foreach ($categories as $catOdooId) {
                $catMageId = $this->repoOdooCat->getMageIdByOdooId($catOdooId);
                if (!in_array($catMageId, $catsExist)) {
                    /* create new product link if not exists */
                    /** @var \Magento\Catalog\Api\Data\CategoryProductLinkInterface $prodLink */
                    $prodLink = $this->factCatProdLink->create();
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