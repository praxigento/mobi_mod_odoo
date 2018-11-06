<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Block\Adminhtml\Sales\Replicate\Orders;


class Index
    extends \Magento\Backend\Block\Template
{
    /** @var \Magento\Sales\Api\Data\OrderInterface[] */
    private $cacheOrders;
    /** @var \Praxigento\Odoo\Helper\Replicate\Orders\Collector */
    private $hlpCollector;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Praxigento\Odoo\Helper\Replicate\Orders\Collector $hlpCollector,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->hlpCollector = $hlpCollector;
    }

    protected function _beforeToHtml()
    {
        $this->cacheOrders = $this->hlpCollector->getOrdersToReplicate();
        $result = parent::_beforeToHtml();
        return $result;
    }

    public function getTotalOrders()
    {
        $result = count($this->cacheOrders);
        return $result;
    }

    public function getUrlSubmit()
    {
        $result = $this->getUrl('*/*/report');
        return $result;
    }
}