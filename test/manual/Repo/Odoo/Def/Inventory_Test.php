<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Def;

use Magento\Framework\Webapi\ServiceInputProcessor;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\Def\Login;
use Praxigento\Odoo\Repo\Odoo\Connector\Base\Adapter;
use Praxigento\Odoo\Repo\Odoo\Connector\Base\RestRequest;
use Praxigento\Odoo\Repo\Odoo\Connector\Config\Def\Params;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Inventory_ManualTest extends \Praxigento\Core\Lib\Test\BaseIntegrationTest
{
    /** @var  Inventory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $converter = $this->_manObj->get(ServiceInputProcessor::class);
        $logger = $this->_manObj->get(\Praxigento\Odoo\Logger::class);
        $adapter = $this->_manObj->get(Adapter::class);
        $params = new Params([
            'BaseUri' => 'http://lion.host.prxgt.com:8122',
            'DbName' => 'oe_odoo9_api',
            'UserName' => 'admin',
            'UserPassword' => 'admin'
        ]);
        $login = new Login($logger, $adapter, $params);
        $rest = new RestRequest($logger, $adapter, $params, $login);
        $this->obj = new Inventory($converter, $rest);
    }

    public function test_get()
    {
        $res = $this->obj->get(428);
        $this->assertNotNull($res);
    }

//    public function test_replicate()
//    {
//        $res = $this->obj->get();
//        /** @var \Praxigento\Odoo\Service\IReplicate $call */
//        $call = $this->_manObj->get(\Praxigento\Odoo\Service\IReplicate::class);
//        /** @var \Praxigento\Odoo\Service\Replicate\Request\ProductSave $req */
//        $req = $this->_manObj->create(\Praxigento\Odoo\Service\Replicate\Request\ProductSave::class);
//        $req->setProductBundle($res);
//        $resp = $call->productSave($req);
//        $this->assertNotNull($res);
//    }

}