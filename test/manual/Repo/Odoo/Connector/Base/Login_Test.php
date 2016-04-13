<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector\Base;


include_once(__DIR__ . '/../../../../phpunit_bootstrap.php');

class Login_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{
    /** @var  Login */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $logger = $this->_manObj->get(\Praxigento\Odoo\Logger::class);
        $adapter = $this->_manObj->get(Adapter::class);
        $this->obj = new Login(
            $logger,
            $adapter,
            'http://lion.host.prxgt.com:8122',
            'oe_odoo9_api',
            'admin',
            'admin'
        );
    }

    public function test_getUserId()
    {
        $this->obj->getUserId();
        1 + 1;
    }

}