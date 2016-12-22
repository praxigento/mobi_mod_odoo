<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Setup;

use Praxigento\Odoo\Data\Entity\Category;
use Praxigento\Odoo\Data\Entity\Customer;
use Praxigento\Odoo\Data\Entity\Lot;
use Praxigento\Odoo\Data\Entity\Product;
use Praxigento\Odoo\Data\Entity\Registry\Request;
use Praxigento\Odoo\Data\Entity\SaleOrder;
use Praxigento\Odoo\Data\Entity\Warehouse;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{
    protected function _setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Odoo';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Registry / Request */
        $entityAlias = Request::ENTITY_NAME;
        $demEntity = $demPackage->get('package/Registry/entity/Request');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Category */
        $entityAlias = Category::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Category');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Customer */
        $entityAlias = Customer::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Customer');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Lot */
        $entityAlias = Lot::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Lot');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Product */
        $entityAlias = Product::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Product');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* SaleOrder */
        $entityAlias = SaleOrder::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/SaleOrder');
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Warehouse */
        $entityAlias = Warehouse::ENTITY_NAME;
        $demEntity = $demPackage->get('entity/Warehouse');
        $this->_toolDem->createEntity($entityAlias, $demEntity);
    }


}