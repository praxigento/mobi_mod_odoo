<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Praxigento\Odoo\Block\Adminhtml\Sales\Replicate\Orders;


class Report
    extends \Magento\Backend\Block\Template
{
    /** @var \Praxigento\Odoo\Api\App\Logger\Main */
    private $logger;
    /** @var \Praxigento\Odoo\Service\Replicate\Sale\Orders */
    private $servReplicate;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Praxigento\Odoo\Api\App\Logger\Main $logger,
        \Praxigento\Odoo\Service\Replicate\Sale\Orders $servReplicate,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->logger = $logger;
        $this->servReplicate = $servReplicate;
    }

    protected function _beforeToHtml()
    {
        $req = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Request();
        $resp = $this->servReplicate->exec($req);
        $result = parent::_beforeToHtml();
        return $result;
    }

    public function outLog()
    {
        $hndl = $this->logger->getHandlerMemory();
        $stream = $hndl->getStream();
        if ($stream) {
            rewind($stream);
            $result = stream_get_contents($stream);
        } else {
            $result = __('No data.');
        }
        return $result;
    }
}