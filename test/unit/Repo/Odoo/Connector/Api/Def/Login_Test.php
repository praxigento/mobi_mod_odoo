<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector\Api\Def;

use Flancer32\Lib\DataObject;

include_once(__DIR__ . '/../../../../../phpunit_bootstrap.php');

class Login_UnitTest extends \Praxigento\Core\Test\BaseMockeryCase
{
    /** @var  \Mockery\MockInterface */
    private $mAdapter;
    /** @var  \Mockery\MockInterface */
    private $mConfig;
    /** @var  \Mockery\MockInterface */
    private $mLogger;
    /** @var  \Mockery\MockInterface */
    private $mManObj;
    /** @var  Login */
    private $obj;

    protected function setUp()
    {
        parent::setUp();
        $this->mLogger = $this->_mockLogger();
        $this->mManObj = $this->_mockObjectManager();
        $this->mAdapter = $this->_mock(\Praxigento\Odoo\Repo\Odoo\Connector\Sub\Adapter::class);
        $this->mConfig = $this->_mock(\Praxigento\Odoo\Repo\Odoo\Connector\Config\IAuthentication::class);
        // public function __construct(
        // $this->_authBaseUrl = $params->getBaseUri();
        $this->mConfig->shouldReceive('getBaseUri')->once();
        // $this->_authDb = $params->getDbName();
        $this->mConfig->shouldReceive('getDbName')->once();
        // $this->_authUser = $params->getUserName();
        $this->mConfig->shouldReceive('getUserName')->once();
        // $this->_authPasswd = $params->getUserPassword();
        $this->mConfig->shouldReceive('getUserPassword')->once();
        $this->obj = new Login(
            $this->mLogger,
            $this->mManObj,
            $this->mAdapter,
            $this->mConfig
        );
    }

    public function test_cacheReset()
    {
        $this->obj->cacheReset();
    }

    public function test_getSessionId()
    {
        /** === Test Data === */
        $USER_ID = 'user id';
        $SESS_ID = 'session id';
        $PARAMS_JSON = 'json params';
        $CONTENT = 'content';
        $CONTENT_DATA = 'content data';
        /** === Setup Mocks === */
        // $request = $this->_adapter->encodeJson($params);
        $this->mAdapter
            ->shouldReceive('encodeJson')->once()
            ->andReturn($PARAMS_JSON);
        // $context = $this->_adapter->createContext($contextOpts);
        $this->mAdapter
            ->shouldReceive('createContext')->once()
            ->andReturn($PARAMS_JSON);
        // $contents = $this->_adapter->getContents($uri, $context);
        $this->mAdapter
            ->shouldReceive('getContents')->once()
            ->andReturn($CONTENT);
        // $data = $this->_adapter->decodeJson($contents);
        $this->mAdapter
            ->shouldReceive('decodeJson')->once()
            ->with($CONTENT)
            ->andReturn($CONTENT_DATA);
        // $respData = $this->_manObj->create(DataObject::class, ['arg1' => $response]);
        $mRespData = $this->_mock(DataObject::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mRespData);
        // $this->_cachedOdooUserId = $respData->getData(self::ODOO_PATH_USER_ID);
        $mRespData->shouldReceive('getData')->once()
            ->andReturn($USER_ID);
        // $this->_cachedOdooSessionId = $respData->getData(self::ODOO_PATH_SESSION_ID);
        $mRespData->shouldReceive('getData')->once()
            ->andReturn($SESS_ID);
        // $this->_logger->info($msg);
        $this->mLogger
            ->shouldReceive('info')->once();
        /** === Call and asserts  === */
        $res = $this->obj->getSessionId();
        $this->assertEquals($SESS_ID, $res);
    }

    /**
     * @expectedException \Exception
     */
    public function test_getSessionId_failedInOdoo()
    {
        /** === Test Data === */
        $PARAMS_JSON = 'json params';
        /** === Setup Mocks === */
        // $request = $this->_adapter->encodeJson($params);
        $this->mAdapter
            ->shouldReceive('encodeJson')->once()
            ->andReturn($PARAMS_JSON);
        // $context = $this->_adapter->createContext($contextOpts);
        $this->mAdapter
            ->shouldReceive('createContext')->once()
            ->andReturn($PARAMS_JSON);
        // $contents = $this->_adapter->getContents($uri, $context);
        $this->mAdapter
            ->shouldReceive('getContents')->once()
            ->andReturn(false);
        // $this->_logger->critical($msg);
        $this->mLogger
            ->shouldReceive('critical')->once();
        /** === Call and asserts  === */
        $this->obj->getSessionId();
    }

    /**
     * @expectedException \Exception
     */
    public function test_getSessionId_wrongAuth()
    {
        /** === Test Data === */
        $PARAMS_JSON = 'json params';
        $CONTENTS = 'odoo response';
        $RESPONSE = [];
        $USER_ID = 32;
        $SESS_ID = 'sessionId';
        /** === Setup Mocks === */
        // $request = $this->_adapter->encodeJson($params);
        $this->mAdapter
            ->shouldReceive('encodeJson')->once()
            ->andReturn($PARAMS_JSON);
        // $context = $this->_adapter->createContext($contextOpts);
        $this->mAdapter
            ->shouldReceive('createContext')->once()
            ->andReturn($PARAMS_JSON);
        // $contents = $this->_adapter->getContents($uri, $context);
        $this->mAdapter
            ->shouldReceive('getContents')->once()
            ->andReturn($CONTENTS);
        // $response = $this->_adapter->decodeJson($contents);
        $this->mAdapter
            ->shouldReceive('decodeJson')->once()
            ->andReturn($RESPONSE);
        // $respData = $this->_manObj->create(DataObject::class, ['arg1' => $response]);
        $mRespData = $this->_mock(\Flancer32\Lib\DataObject::class);
        $this->mManObj
            ->shouldReceive('create')->once()
            ->andReturn($mRespData);
        // $this->_cachedOdooUserId = $respData->getData(self::ODOO_PATH_USER_ID);
        $mRespData
            ->shouldReceive('getData')->once()
            ->with(\Praxigento\Odoo\Repo\Odoo\Connector\Api\Def\Login::ODOO_PATH_USER_ID)
            ->andReturn(null);
        /** === Call and asserts  === */
        $this->obj->getSessionId();
    }

    public function test_getUserId()
    {
        /** === Test Data === */
        $USER_ID = 'user id';
        $REQUEST = 'xml request';
        $CONTEXT = 'context';
        $CONTENT = 'content';
        /** === Setup Mocks === */
        // $request = $this->_adapter->encodeXml('login', $params, $outOpts);
        $this->mAdapter
            ->shouldReceive('encodeXml')->once()
            ->andReturn($REQUEST);
        // $context = $this->_adapter->createContext($contextOpts);
        $this->mAdapter
            ->shouldReceive('createContext')->once()
            ->andReturn($CONTEXT);
        // $contents = $this->_adapter->getContents($uri, $context);
        $this->mAdapter
            ->shouldReceive('getContents')->once()
            ->andReturn($CONTENT);
        // $this->_cachedOdooUserId = $this->_adapter->decodeXml($contents);
        $this->mAdapter
            ->shouldReceive('decodeXml')->once()
            ->with($CONTENT)
            ->andReturn($USER_ID);
        // $this->_logger->info($msg);
        $this->mLogger
            ->shouldReceive('info')->once();
        /** === Call and asserts  === */
        $res = $this->obj->getUserId();
        $this->assertEquals($USER_ID, $res);
    }

    /**
     * @expectedException \Exception
     */
    public function test_getUserId_failed()
    {
        /** === Test Data === */
        $USER_ID = 'user id';
        $REQUEST = 'xml request';
        $CONTEXT = 'context';
        /** === Setup Mocks === */
        // $request = $this->_adapter->encodeXml('login', $params, $outOpts);
        $this->mAdapter
            ->shouldReceive('encodeXml')->once()
            ->andReturn($REQUEST);
        // $context = $this->_adapter->createContext($contextOpts);
        $this->mAdapter
            ->shouldReceive('createContext')->once()
            ->andReturn($CONTEXT);
        // $contents = $this->_adapter->getContents($uri, $context);
        $this->mAdapter
            ->shouldReceive('getContents')->once()
            ->andReturn(false);
        // $this->_logger->critical($msg);
        $this->mLogger
            ->shouldReceive('critical')->once();
        /** === Call and asserts  === */
        $res = $this->obj->getUserId();
        $this->assertEquals($USER_ID, $res);
    }
}