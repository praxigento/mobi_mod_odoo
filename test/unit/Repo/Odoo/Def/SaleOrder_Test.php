<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Def;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class SaleOrder_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mMageSrvInProc;
    /** @var  \Mockery\MockInterface */
    private $mRest;
    /** @var  \Praxigento\Odoo\Repo\Odoo\Def\SaleOrder */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        /** create mocks */
        $this->mMageSrvInProc = $this->_mock(\Magento\Framework\Webapi\ServiceInputProcessor::class);
        $this->mRest = $this->_mock(\Praxigento\Odoo\Repo\Odoo\Connector\Rest::class);
        /** setup mocks for constructor */
        /** create object to test */
        $this->obj = new \Praxigento\Odoo\Repo\Odoo\Def\SaleOrder(
            $this->mMageSrvInProc,
            $this->mRest
        );
    }

    public function test_constructor()
    {
        /** === Call and asserts  === */
        $this->assertInstanceOf(\Praxigento\Odoo\Repo\Odoo\ISaleOrder::class, $this->obj);
    }

    public function test_save_fail()
    {
        /** === Test Data === */
        $ORDER = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder::class);
        /** === Setup Mocks === */
        // $underscored = $order->getData(null, true);
        $mUnderscored = [];
        $ORDER->shouldReceive('getData')->once()
            ->andReturn($mUnderscored);
        // $cover = $this->_rest->request($underscored, self::ROUTE);
        $mCover = $this->_mock(\Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\ICover::class);
        $this->mRest
            ->shouldReceive('request')->once()
            ->andReturn($mCover);
        // $data = $cover->getResultData();
        $mData = null;
        $mCover
            ->shouldReceive('getResultData')->once()
            ->andReturn($mData);
        // $error = $cover->getError();
        $mError = 'error';
        $mCover
            ->shouldReceive('getError')->once()
            ->andReturn($mError);
        // $result = $this->_mageSrvInProc->convertValue($error, \Praxigento\Odoo\Data\Odoo\Error::class);
        $mResult = $this->_mock(\Praxigento\Odoo\Data\Odoo\Error::class);
        $this->mMageSrvInProc
            ->shouldReceive('convertValue')->once()
            ->andReturn($mResult);
        /** === Call and asserts  === */
        $res = $this->obj->save($ORDER);
        $this->assertTrue($res instanceof \Praxigento\Odoo\Data\Odoo\Error);
    }

    public function test_save_success()
    {
        /** === Test Data === */
        $ORDER = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder::class);
        /** === Setup Mocks === */
        // $underscored = $order->getData(null, true);
        $mUnderscored = [];
        $ORDER->shouldReceive('getData')->once()
            ->andReturn($mUnderscored);
        // $cover = $this->_rest->request($underscored, self::ROUTE);
        $mCover = $this->_mock(\Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\ICover::class);
        $this->mRest
            ->shouldReceive('request')->once()
            ->andReturn($mCover);
        // $data = $cover->getResultData();
        $mData = 'data';
        $mCover
            ->shouldReceive('getResultData')->once()
            ->andReturn($mData);
        // $result = $this->_mageSrvInProc->convertValue($data, \Praxigento\Odoo\Data\Odoo\SaleOrder\Response::class);
        $mResult = $this->_mock(\Praxigento\Odoo\Data\Odoo\SaleOrder\Response::class);
        $this->mMageSrvInProc
            ->shouldReceive('convertValue')->once()
            ->andReturn($mResult);
        /** === Call and asserts  === */
        $res = $this->obj->save($ORDER);
        $this->assertTrue($res instanceof \Praxigento\Odoo\Data\Odoo\SaleOrder\Response);
    }
}