<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector\Config\Def;

use Praxigento\Odoo\Repo\Odoo\Connector\Config\IAuthentication;
use Praxigento\Odoo\Repo\Odoo\Connector\Config\IConnection;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class Params_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{

    public function test_accessors()
    {
        /* === Test Data === */
        $URI = 'uri';
        $BASE_NAME = 'base';
        $USER_NAME = 'user';
        $USER_PASSWORD = 'password';
        /* === Call and asserts  === */
        $obj = new Params();
        $this->assertInstanceOf(IConnection::class, $obj);
        $this->assertInstanceOf(IAuthentication::class, $obj);
        $obj->setBaseUri($URI);
        $obj->setDbName($BASE_NAME);
        $obj->setUserName($USER_NAME);
        $obj->setUserPassword($USER_PASSWORD);
        $this->assertEquals($URI, $obj->getBaseUri());
        $this->assertEquals($BASE_NAME, $obj->getDbName());
        $this->assertEquals($USER_NAME, $obj->getUserName());
        $this->assertEquals($USER_PASSWORD, $obj->getUserPassword());
    }

}