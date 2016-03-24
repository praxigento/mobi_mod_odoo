<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Data\Product\Replicator;

/**
 * Bundle of the products (or one product) to replicate data between Magento and Odoo.
 */
interface IBundle
{
    /**
     * @return \Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\ILot[]|null
     */
    public function getLot();

    /**
     * Products bundle options.
     *
     * @return \Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\IOption|null
     */
    public function getOption();

    /**
     * @return \Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\IProduct[]|null
     */
    public function getProduct();

    /**
     * Warehouses related to products in the bundle.
     *
     * @return \Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\IWarehouse[]|null
     */
    public function getWarehouse();

    /**
     * @param \Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\ILot[] $data
     */
    public function setLot($data = null);

    /**
     * Products bundle options.
     *
     * @param \Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\IOption $data
     */
    public function setOption(Bundle\IOption $data = null);

    /**
     * @param \Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\IProduct[] $data
     */
    public function setProduct($data = null);

    /**
     * Warehouses related to products in the bundle.
     *
     * @param \Praxigento\Odoo\Api\Data\Product\Replicator\Bundle\IWarehouse[] $data
     */
    public function setWarehouse($data = null);
}