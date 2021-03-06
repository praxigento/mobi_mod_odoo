<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Odoo\Api\Web\Sales\Order\PushRepeat;

use Praxigento\Odoo\Api\Web\Sales\Order\PushRepeat\Response as AnObject;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class ResponseTest
    extends \Praxigento\Core\Test\BaseCase\Unit
{
    private function getDataEntries()
    {
        $result = new \Praxigento\Odoo\Service\Replicate\Sale\Orders\Response\Entry();
        $result->setDebug('debug');
        $result->setErrorName('error');
        $result->setIsSucceed(true);
        $result->setNumber('number');
        return [$result];
    }

    public function test_convert()
    {
        /* create object & convert it to 'JSON'-array */
        $obj = new AnObject();

        $entries = $this->getDataEntries();

        $data = new \Praxigento\Odoo\Api\Web\Sales\Order\PushRepeat\Response\Data();
        $data->setEntries($entries);
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