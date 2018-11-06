<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Block\Adminhtml\Catalog\Replicate\Products;

use Praxigento\Warehouse\Repo\Data\Warehouse as EWrhs;

class Index
    extends \Magento\Backend\Block\Template
{
    const NO_WRHS = 0;
    /** @var \Praxigento\Warehouse\Repo\Dao\Warehouse */
    private $daoWrhs;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Praxigento\Warehouse\Repo\Dao\Warehouse $daoWrhs,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->daoWrhs = $daoWrhs;
    }

    public function getSelectItemsForWarehouses()
    {
        $result = [];
        /** @var EWrhs[] $all */
        $all = $this->daoWrhs->get();
        foreach ($all as $one) {
            $label = $one->getCode();
            $result[$label] = $label;
        }
        asort($result);
        $first = [self::NO_WRHS => '&nbsp;'];
        $result = $first + $result;
        return $result;
    }

    public function getUrlSubmit()
    {
        $result = $this->getUrl('*/*/report');
        return $result;
    }
}