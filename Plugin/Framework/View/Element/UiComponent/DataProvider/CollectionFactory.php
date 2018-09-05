<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Plugin\Framework\View\Element\UiComponent\DataProvider;

use Praxigento\Odoo\Config as Cfg;
use Praxigento\Odoo\Plugin\Framework\View\Element\UiComponent\DataProvider\CollectionFactory\A\QueryModifier as AQueryMod;

/**
 * Add Odoo replication flag to data collection for sale orders grid.
 */
class CollectionFactory
{
    /** @var  AQueryMod */
    private $aQueryMod;

    public function __construct(
        AQueryMod $aQueryMod
    ) {
        $this->aQueryMod = $aQueryMod;
    }

    /**
     * Modify result collection for "sales_order_grid_data_source" (add joins & filter mapping, MOBI-357).
     *
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Closure $proceed
     * @param $requestName
     * @return null
     */
    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == Cfg::DS_SALES_ORDERS_GRID) {
            if ($result instanceof \Magento\Sales\Model\ResourceModel\Order\Grid\Collection) {
                /* add JOINS to the select query */
                $this->aQueryMod->populateSelect($result);
                /* add fields to mapping */
                $this->aQueryMod->addFieldsMapping($result);
            }
        }
        return $result;
    }
}