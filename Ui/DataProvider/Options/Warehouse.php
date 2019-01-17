<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2019
 */

namespace Praxigento\Odoo\Ui\DataProvider\Options;

use Praxigento\Odoo\Repo\Data\Warehouse as EWrhs;

class Warehouse
    implements \Magento\Framework\Data\OptionSourceInterface
{
    public const VAL_ALL = '.all';

    /** @var \Praxigento\Odoo\Repo\Dao\Warehouse */
    private $daoWrhs;
    /** @var array */
    private $options;

    public function __construct(
        \Praxigento\Odoo\Repo\Dao\Warehouse $daoWrhs
    ) {
        $this->daoWrhs = $daoWrhs;
    }

    /**
     * @return EWrhs[]
     */
    private function loadItems()
    {
        $order = EWrhs::A_ODOO_REF . ' ASC';
        $rs = $this->daoWrhs->get(null, $order);
        return $rs;
    }

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            $items = $this->loadItems();
            foreach ($items as $item) {
                $value = $item->getOdooRef();
                $option = ["label" => $value, "value" => $value];
                $this->options[] = $option;
            }
            /* place 'All' as the first item */
            $option = ["label" => "All", "value" => self::VAL_ALL];
            array_unshift($this->options, $option);
        }
        return $this->options;
    }
}
