<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Odoo\Api\Web\Product\Replicate\Save;

use Praxigento\Odoo\Api\Web\Product\Replicate\Save\Request as AnObject;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class RequestTest
    extends \Praxigento\Core\Test\BaseCase\Unit
{
    private function getDataLots()
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Lot();
        $result->setExpirationDate('date');
        $result->setIdOdoo(21);
        $result->setNumber('num');
        return [$result];
    }

    private function getDataOption()
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Option();
        $result->setCurrency('LVL');
        return $result;
    }

    private function getDataProducts()
    {
        $warehouses = $this->getDataProductsWrhs();
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product();
        $result->setCategories([1, 2, 3, 4]);
        $result->setIdOdoo(21);
        $result->setIsActive(true);
        $result->setName('name');
        $result->setPriceRetail(32.23);
        $result->setPriceWholesale(23.32);
        $result->setPvWholesale(23432);
        $result->setSku('sku');
        $result->setWarehouses($warehouses);
        $result->setWeight(43.56);
        return [$result];
    }

    private function getDataProductsWrhs()
    {
        $lots = $this->getDataProductsWrhsLots();
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse();
        $result->setIdOdoo(32);
        $result->setLots($lots);
        return [$result];
    }

    private function getDataProductsWrhsLots()
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Product\Warehouse\Lot();
        $result->setIdOdoo(65);
        $result->setQuantity(98.23);
        return [$result];
    }

    private function getDataWarehouses()
    {
        $result = new \Praxigento\Odoo\Repo\Odoo\Data\Inventory\Warehouse();
        $result->setCode('warehouse');
        $result->setCurrency('LVL');
        $result->setIdOdoo(21);
        return [$result];
    }

    public function test_convert()
    {
        /* create object & convert it to 'JSON'-array */
        $obj = new AnObject();

        $lots = $this->getDataLots();
        $option = $this->getDataOption();
        $products = $this->getDataProducts();
        $warehouses = $this->getDataWarehouses();

        $data = new \Praxigento\Odoo\Api\Web\Product\Replicate\Save\Request\Data();
        $data->setLots($lots);
        $data->setOption($option);
        $data->setProducts($products);
        $data->setWarehouses($warehouses);
        $obj->setData($data);

        /** @var \Magento\Framework\Webapi\ServiceOutputProcessor $output */
        $output = $this->manObj->get(\Magento\Framework\Webapi\ServiceOutputProcessor::class);
        $json = $output->convertValue($obj, AnObject::class);

        /* convert 'JSON'-array to object */
        /** @var \Magento\Framework\Webapi\ServiceInputProcessor $input */
        $input = $this->manObj->get(\Magento\Framework\Webapi\ServiceInputProcessor::class);
        $data = $input->convertValue($json, AnObject::class);
        $this->assertNotNull($data);
    }
}