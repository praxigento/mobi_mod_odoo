<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector\Api\Def;

use Praxigento\Odoo\Repo\Odoo\Connector\Config\Def\Params;
use Praxigento\Odoo\Repo\Odoo\Connector\Sub\Adapter;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class Login_ManualTest extends \Praxigento\Core\Test\BaseIntegrationTest
{
    /** @var  Login */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $logger = $this->_manObj->get(\Praxigento\Odoo\Logger::class);
        $adapter = $this->_manObj->get(Adapter::class);
        $params = new Params([
            'BaseUri' => 'http://lion.host.prxgt.com:8122',
            'DbName' => 'oe_odoo9_api',
            'UserName' => 'admin',
            'UserPassword' => 'admin'
        ]);
        $this->obj = new Login($logger, $this->_manObj, $adapter, $params);
    }

    public function test_SessionId()
    {
        $this->obj->getSessionId();
        return;
    }

    public function test_getUserId()
    {
        $this->obj->getUserId();
        return;
    }

}