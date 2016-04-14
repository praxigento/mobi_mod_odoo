<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Def;

use Praxigento\Odoo\Api\Data\IBundle;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\ICover;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Inventory_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mMageSrvInProc;
    /** @var  \Mockery\MockInterface */
    private $mRest;
    /** @var  Inventory */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /* create mocks */
        $this->mMageSrvInProc = $this->_mock(\Magento\Framework\Webapi\ServiceInputProcessor::class);
        $this->mRest = $this->_mock(\Praxigento\Odoo\Repo\Odoo\Connector\Rest::class);
        /* setup mocks for constructor */
        /* create object to test */
        $this->obj = new Inventory(
            $this->mMageSrvInProc,
            $this->mRest
        );
    }

    public function test_get_emptyParams()
    {
        /* === Test Data === */
        $DATA = 'data';
        $RESULT = 'result';
        /* === Setup Mocks === */
        // $cover = $this->_rest->request($params, self::ROUTE);
        $mCover = $this->_mock(ICover::class);
        $this->mRest
            ->shouldReceive('request')->once()
            ->with([Inventory::ODOO_IDS => []], Inventory::ROUTE)
            ->andReturn($mCover);
        $mCover->shouldReceive('getResultData')->once()
            ->andReturn($DATA);
        // $result = $this->_mageSrvInProc->convertValue($data, IBundle::class);
        $this->mMageSrvInProc
            ->shouldReceive('convertValue')->once()
            ->with($DATA, IBundle::class)
            ->andReturn($RESULT);
        /* === Call and asserts  === */
        $res = $this->obj->get();
        $this->assertEquals($RESULT, $res);
    }

    public function test_get_paramIsArray()
    {
        /* === Test Data === */
        $PARAM = [2, 3];
        $DATA = 'data';
        $RESULT = 'result';
        /* === Setup Mocks === */
        // $cover = $this->_rest->request($params, self::ROUTE);
        $mCover = $this->_mock(ICover::class);
        $this->mRest
            ->shouldReceive('request')->once()
            ->with([Inventory::ODOO_IDS => $PARAM], Inventory::ROUTE)
            ->andReturn($mCover);
        $mCover->shouldReceive('getResultData')->once()
            ->andReturn($DATA);
        // $result = $this->_mageSrvInProc->convertValue($data, IBundle::class);
        $this->mMageSrvInProc
            ->shouldReceive('convertValue')->once()
            ->with($DATA, IBundle::class)
            ->andReturn($RESULT);
        /* === Call and asserts  === */
        $res = $this->obj->get($PARAM);
        $this->assertEquals($RESULT, $res);
    }

    public function test_get_paramIsInt()
    {
        /* === Test Data === */
        $PARAM = 43;
        $DATA = 'data';
        $RESULT = 'result';
        /* === Setup Mocks === */
        // $cover = $this->_rest->request($params, self::ROUTE);
        $mCover = $this->_mock(ICover::class);
        $this->mRest
            ->shouldReceive('request')->once()
            ->with([Inventory::ODOO_IDS => [$PARAM]], Inventory::ROUTE)
            ->andReturn($mCover);
        $mCover->shouldReceive('getResultData')->once()
            ->andReturn($DATA);
        // $result = $this->_mageSrvInProc->convertValue($data, IBundle::class);
        $this->mMageSrvInProc
            ->shouldReceive('convertValue')->once()
            ->with($DATA, IBundle::class)
            ->andReturn($RESULT);
        /* === Call and asserts  === */
        $res = $this->obj->get($PARAM);
        $this->assertEquals($RESULT, $res);
    }

}