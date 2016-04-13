<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector\Api\Def;

use Praxigento\Odoo\Repo\Odoo\Connector\Base\Adapter;
use Praxigento\Odoo\Repo\Odoo\Connector\Config\Def\Params;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class Inventory_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{
    /** @var  Inventory */
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
        $login = new Login($logger, $adapter, $params);
        $this->obj = new Inventory($logger, $adapter, $params, $login);
    }

    public function test_get()
    {
        $this->obj->get();
        1 + 1;
    }

}