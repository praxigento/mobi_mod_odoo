<?php
/**
 * Create DB schema.
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Praxigento\Odoo\Lib\Data\Entity\Lot;
use Praxigento\Odoo\Lib\Data\Entity\Product;
use Praxigento\Odoo\Lib\Data\Entity\Warehouse;

class InstallSchema extends \Praxigento\Core\Setup\Schema\Base
{
    protected function _setup(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** Read and parse JSON schema. */
        $pathToFile = __DIR__ . '/../etc/dem.json';
        $pathToNode = '/dBEAR/package/Praxigento/package/Odoo';
        $demPackage = $this->_toolDem->readDemPackage($pathToFile, $pathToNode);

        /* Product */
        $entityAlias = Product::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Product'];
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Lot */
        $entityAlias = Lot::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Lot'];
        $this->_toolDem->createEntity($entityAlias, $demEntity);

        /* Warehouse */
        $entityAlias = Warehouse::ENTITY_NAME;
        $demEntity = $demPackage['entity']['Warehouse'];
        $this->_toolDem->createEntity($entityAlias, $demEntity);
    }


}