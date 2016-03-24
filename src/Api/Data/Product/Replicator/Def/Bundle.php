<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Api\Data\Product\Replicator\Def;


use Praxigento\Core\Api\Data\Def\Base;
use Praxigento\Odoo\Api\Data\Product\Replicator;
use Praxigento\Odoo\Api\Data\Product\Replicator\IBundle;

class Bundle extends Base implements IBundle
{
    /**
     * @inheritdoc
     */
    public function getLot()
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function getOption()
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function getProduct()
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function getWarehouse()
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function setLot($data = null)
    {
        parent::setData('lot', $data);
    }

    /**
     * @inheritdoc
     */
    public function setOption(Replicator\Bundle\IOption $data = null)
    {
        parent::setData('option', $data);
    }

    /**
     * @inheritdoc
     */
    public function setProduct($data = null)
    {
        parent::setData('product', $data);
    }

    /**
     * @inheritdoc
     */
    public function setWarehouse($data = null)
    {
        parent::setData('warehouse', $data);
    }
}