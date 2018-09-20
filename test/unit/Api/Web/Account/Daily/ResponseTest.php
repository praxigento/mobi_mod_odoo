<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Odoo\Api\Web\Account\Daily;

use Praxigento\Odoo\Api\Web\Account\Daily\Response as AnObject;

include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class ResponseTest
    extends \Praxigento\Core\Test\BaseCase\Unit
{
    private function getDateItems()
    {
        $result = new \Praxigento\Odoo\Service\Replicate\Account\Daily\Response\Item();
        $result->setCode('code');
        $result->setValue(12.34);
        return [$result];
    }

    private function getDates()
    {
        $items = $this->getDateItems();

        $result = new \Praxigento\Odoo\Api\Web\Account\Daily\Response\Data\Item();
        $result->setDate('date');
        $result->setItems($items);
        return [$result];
    }

    public function test_convert()
    {
        /* create object & convert it to 'JSON'-array */
        $obj = new AnObject();

        $dates = $this->getDates();

        $data = new \Praxigento\Odoo\Api\Web\Account\Daily\Response\Data();
        $data->setDates($dates);
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