<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Service\Replicate\Sub\Replicator;


use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Data\Entity\Category as EntityCategory;
use Praxigento\Odoo\Repo\IModule;

class Category
{
    /** @var   CategoryRepositoryInterface */
    protected $_mageRepoCategory;
    /** @var   ObjectManagerInterface */
    protected $_manObj;
    /** @var IModule */
    protected $_repoMod;

    public function __construct(
        ObjectManagerInterface $manObj,
        CategoryRepositoryInterface $mageRepoCat,
        IModule $repoMod
    ) {
        $this->_manObj = $manObj;
        $this->_mageRepoCategory = $mageRepoCat;
        $this->_repoMod = $repoMod;
    }

    /**
     * @param int[] $cats
     */
    public function checkCategoriesExistence($cats)
    {
        foreach ($cats as $odooId) {
            /* get mageId by odooId from registry */
            $mageId = $this->_repoMod->getMageIdByOdooId(EntityCategory::ENTITY_NAME, $odooId);
            if (!$mageId) {
                $mageId = $this->createMageCategory('Cat #' . $odooId);
                $this->_repoMod->registerMageIdForOdooId(EntityCategory::ENTITY_NAME, $mageId, $odooId);
            }
        }
    }

    public function createMageCategory($name)
    {
        /** @var  $category CategoryInterface */
        $category = $this->_manObj->create(CategoryInterface::class);
        $category->setName($name);
        $category->setIsActive(false);
        $saved = $this->_mageRepoCategory->save($category);
        $result = $saved->getId();
        return $result;
    }
}