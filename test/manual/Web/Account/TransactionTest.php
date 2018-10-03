<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Test\Praxigento\Odoo\Web\Account;

use Praxigento\Odoo\Api\Web\Account\Transaction\Request as ARequest;
use Praxigento\Odoo\Api\Web\Account\Transaction\Request\Data as ARequestData;
use Praxigento\Odoo\Api\Web\Account\Transaction\Response as AResponse;
use Praxigento\Odoo\Api\Web\Account\TransactionInterface as AService;
use Praxigento\Odoo\Config as Cfg;

include_once(__DIR__ . '/../../phpunit_bootstrap.php');

class TransactionTest
    extends \Praxigento\Core\Test\BaseCase\Manual
{
    public function test_exec()
    {
        /** @var AService $obj */
        $obj = $this->manObj->create(AService::class);
        $req = new ARequest();
        $data = new ARequestData ();
        $data->setAssetTypeCode(Cfg::CODE_TYPE_ASSET_PV);
        $data->setCustomerMlmId('778104481');
        $data->setDateFrom('2018-08-01');
        $data->setDateTo('2018-08-31');
        $req->setData($data);
        $res = $obj->exec($req);
        $this->assertInstanceOf(AResponse::class, $res);
    }


}