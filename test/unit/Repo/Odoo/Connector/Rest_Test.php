<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector;

include_once(__DIR__ . '/../../../phpunit_bootstrap.php');

class Rest_UnitTest extends \Praxigento\Core\Lib\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mAdapter;
    /** @var  \Mockery\MockInterface */
    private $mConfig;
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mLogin;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  Rest */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->mLogger = $this->_mockLogger();
        $this->mManObj = $this->_mockObjectManager();
        $this->mAdapter = $this->_mock(\Praxigento\Odoo\Repo\Odoo\Connector\Sub\Adapter::class);
        $this->mConfig = $this->_mock(\Praxigento\Odoo\Repo\Odoo\Connector\Config\IConnection::class);
        $this->mLogin = $this->_mock(\Praxigento\Odoo\Repo\Odoo\Connector\Api\ILogin::class);
        // public function __construct(
        // $this->_baseUri = $params->getBaseUri();
        $this->mConfig->shouldReceive('getBaseUri')->once();
        $this->obj = new Rest(
            $this->mLogger,
            $this->mManObj,
            $this->mAdapter,
            $this->mConfig,
            $this->mLogin
        );
    }

    public function test_request()
    {
        /* === Test Data === */
        $PARAMS = 'params';
        $PARAMS_JSON = 'JSON encoded params';
        $ROUTE = 'route';
        $SESS_ID = 'session id';
        $CONTEXT_OPTS_JSON = 'JSON encoded context options';
        $CONTENT = 'content';
        $CONTENT_DATA = 'data';
        $RESULT = 'result';
        /* === Setup Mocks === */
        // $sessId = $this->_login->getSessionId();
        $this->mLogin
            ->shouldReceive('getSessionId')->once()
            ->andReturn($SESS_ID);
        // $request = $this->_adapter->encodeJson($params);
        $this->mAdapter
            ->shouldReceive('encodeJson')->once()
            ->with($PARAMS)
            ->andReturn($PARAMS_JSON);
        // $context = $this->_adapter->createContext($contextOpts);
        $this->mAdapter
            ->shouldReceive('createContext')->once()
            ->andReturn($PARAMS_JSON);
        // this->_logger->debug("Request URI:\t$uri");
        $this->mLogger
            ->shouldReceive('debug');
        // $jsonContextOpt = $this->_adapter->encodeJson($contextOpts);
        $this->mAdapter
            ->shouldReceive('encodeJson')->once()
            ->andReturn($CONTEXT_OPTS_JSON);
        // $contents = $this->_adapter->getContents($uri, $context);
        $this->mAdapter
            ->shouldReceive('getContents')->once()
            ->andReturn($CONTENT);
        // $data = $this->_adapter->decodeJson($contents);
        $this->mAdapter
            ->shouldReceive('decodeJson')->once()
            ->with($CONTENT)
            ->andReturn($CONTENT_DATA);
        // $result = $this->_manObj->create(Cover::class, ['arg1' => $data]);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($RESULT);
        /* === Call and asserts  === */
        $res = $this->obj->request($PARAMS, $ROUTE);
        $this->assertEquals($RESULT, $res);
    }

    /**
     * @expectedException \Exception
     */
    public function test_request_false()
    {
        /* === Test Data === */
        $PARAMS = 'params';
        $PARAMS_JSON = 'JSON encoded params';
        $ROUTE = 'route';
        $SESS_ID = 'session id';
        $CONTEXT_OPTS_JSON = 'JSON encoded context options';
        $CONTENT = 'content';
        $CONTENT_DATA = 'data';
        $RESULT = 'result';
        /* === Setup Mocks === */
        // $sessId = $this->_login->getSessionId();
        $this->mLogin
            ->shouldReceive('getSessionId')->once()
            ->andReturn($SESS_ID);
        // $request = $this->_adapter->encodeJson($params);
        $this->mAdapter
            ->shouldReceive('encodeJson')->once()
            ->with($PARAMS)
            ->andReturn($PARAMS_JSON);
        // $context = $this->_adapter->createContext($contextOpts);
        $this->mAdapter
            ->shouldReceive('createContext')->once()
            ->andReturn($PARAMS_JSON);
        // this->_logger->debug("Request URI:\t$uri");
        $this->mLogger
            ->shouldReceive('debug');
        // $jsonContextOpt = $this->_adapter->encodeJson($contextOpts);
        $this->mAdapter
            ->shouldReceive('encodeJson')->once()
            ->andReturn($CONTEXT_OPTS_JSON);
        // $contents = $this->_adapter->getContents($uri, $context);
        $this->mAdapter
            ->shouldReceive('getContents')->once()
            ->andReturn(false);
        // $this->_logger->critical($msg);
        $this->mLogger
            ->shouldReceive('critical')->once();
        /* === Call and asserts  === */
        $res = $this->obj->request($PARAMS, $ROUTE);
        $this->assertEquals($RESULT, $res);
    }

}