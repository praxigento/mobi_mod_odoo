<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Entity\Def;

use Magento\Framework\App\ResourceConnection;
use Praxigento\Core\Repo\Def\Entity as BaseEntityRepo;
use Praxigento\Core\Repo\IGeneric as IRepoBasic;
use Praxigento\Odoo\Data\Entity\Warehouse as Entity;
use Praxigento\Odoo\Repo\Entity\IWarehouse as IEntityRepo;

class Warehouse extends BaseEntityRepo implements IEntityRepo
{
    public function __construct(
        ResourceConnection $resource,
        IRepoBasic $repoGeneric
    ) {
        parent::__construct($resource, $repoGeneric, new Entity());
    }

}