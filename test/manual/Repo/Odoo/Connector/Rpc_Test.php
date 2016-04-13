<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector;


include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Rpc_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{
    /** @var  Rpc */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $logger = $this->_manObj->get(\Praxigento\Odoo\Logger::class);
        $adapter = $this->_manObj->get(Adapter::class);
        $login = $this->_manObj->get(Login::class);
        $this->obj = new Rpc($logger, $adapter, $login);
    }

    public function test_login()
    {
        $resource = '';
        $operation = '';
        $this->obj->request($resource, $operation);
        1 + 1;
    }

}