<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Test\Praxigento\Odoo\Api\Web\Customer\Pv\Add;

use Praxigento\Odoo\Api\Web\Customer\Pv\Add\Request as AnObject;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class RequestTest
    extends \Praxigento\Core\Test\BaseCase\Unit
{

    public function test_convert()
    {
        /* create object & convert it to 'JSON'-array */
        $obj = new AnObject();

        $data = new \Praxigento\Odoo\Api\Web\Customer\Pv\Add\Request\Data();
        $data->setCustomerMlmId('mlm id');
        $data->setNotes('notes');
        $data->setOdooRef('reference');
        $data->setPv(12.32);
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