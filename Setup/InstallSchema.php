<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Setup;

use Praxigento\Odoo\Repo\Data\Category;
use Praxigento\Odoo\Repo\Data\Customer;
use Praxigento\Odoo\Repo\Data\Lot;
use Praxigento\Odoo\Repo\Data\Product;
use Praxigento\Odoo\Repo\Data\Registry\Request;
use Praxigento\Odoo\Repo\Data\SaleOrder;
use Praxigento\Odoo\Repo\Data\Warehouse;

class InstallSchema extends \Praxigento\Core\App\Setup\Schema\Base
{
    protected function setup()
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Odoo';
        $demPackage = $this->toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Registry / Request */
        $demEntity = $demPackage->get('package/Registry/entity/Request');
        $this->toolDem->createEntity(Request::ENTITY_NAME, $demEntity);

        /* Category */
        $demEntity = $demPackage->get('entity/Category');
        $this->toolDem->createEntity(Category::ENTITY_NAME, $demEntity);

        /* Customer */
        $demEntity = $demPackage->get('entity/Customer');
        $this->toolDem->createEntity(Customer::ENTITY_NAME, $demEntity);

        /* Lot */
        $demEntity = $demPackage->get('entity/Lot');
        $this->toolDem->createEntity(Lot::ENTITY_NAME, $demEntity);

        /* Product */
        $demEntity = $demPackage->get('entity/Product');
        $this->toolDem->createEntity(Product::ENTITY_NAME, $demEntity);

        /* SaleOrder */
        $demEntity = $demPackage->get('entity/SaleOrder');
        $this->toolDem->createEntity(SaleOrder::ENTITY_NAME, $demEntity);

        /* Warehouse */
        $demEntity = $demPackage->get('entity/Warehouse');
        $this->toolDem->createEntity(Warehouse::ENTITY_NAME, $demEntity);
    }

}