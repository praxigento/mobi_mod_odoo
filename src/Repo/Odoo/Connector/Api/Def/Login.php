<?php
/**
 * Implementation of the login operation to get UserId for Odoo API requests.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector\Api\Def;

use Flancer32\Lib\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Praxigento\Core\ICached;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\ILogin;
use Praxigento\Odoo\Repo\Odoo\Connector\Config\IAuthentication;
use Praxigento\Odoo\Repo\Odoo\Connector\Rest;
use Praxigento\Odoo\Repo\Odoo\Connector\Sub\Adapter;
use Psr\Log\LoggerInterface;

class Login implements ICached, ILogin
{
    /* TIMEOUTS: INTR-653 */
    const HTTP_TIMEOUT_SEC_LOGIN = 15;
    /**#@+
     * Odoo API data labels.
     */
    const ODOO_DB_NAME = 'dbname';
    const ODOO_LOGIN = 'login';
    const ODOO_PASSWORD = 'password';
    const ODOO_PATH_SESSION_ID = '/result/session_id';
    const ODOO_PATH_USER_ID = '/result/uid';
    /**#@-*/
    /** @var  Adapter adapter for PHP functions to be mocked in tests */
    protected $_adapter;
    /**
     * OpenERP connection data
     */
    protected $_authBaseUrl;
    protected $_authDb;
    protected $_authPasswd;
    protected $_authUser;
    /** @var int cache for Session ID of the authenticated Odoo REST API user. */
    protected $_cachedOdooSessionId = null;
    /** @var int cache for User ID of the authenticated Odoo XML RPC API user. */
    protected $_cachedOdooUserId = null;
    /** @var  LoggerInterface separate channel to log Odoo activity */
    protected $_logger;
    /** @var ObjectManagerInterface */
    protected $_manObj;


    function __construct(
        LoggerInterface $logger,
        ObjectManagerInterface $manObj,
        Adapter $adapter,
        IAuthentication $params
    ) {
        $this->_logger = $logger;
        $this->_manObj = $manObj;
        $this->_adapter = $adapter;
        $this->_authBaseUrl = $params->getBaseUri();
        $this->_authDb = $params->getDbName();
        $this->_authUser = $params->getUserName();
        $this->_authPasswd = $params->getUserPassword();
    }

    /**
     * Short method to get connection data to trace into log.
     * @return string
     */
    private function _traceConnectionData()
    {
        $result = "Odoo connection data (url/db/user): {$this->_authBaseUrl} / {$this->_authDb} / {$this->_authUser}.";
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function cacheReset()
    {
        $this->_cachedOdooUserId = null;
        $this->_cachedOdooSessionId = null;
    }

    /**
     * @inheritdoc
     */
    public function getSessionId()
    {
        if (is_null($this->_cachedOdooSessionId)) {
            /* request User ID from Odoo */
            $params = [
                self::ODOO_DB_NAME => $this->_authDb,
                self::ODOO_LOGIN => $this->_authUser,
                self::ODOO_PASSWORD => $this->_authPasswd
            ];
            $request = $this->_adapter->encodeJson($params);
            $ctxOpts = [
                'http' => [
                    'method' => Rest::HTTP_METHOD_POST,
                    'header' => 'Content-Type: application/json; charset=utf-8',
                    'timeout' => self::HTTP_TIMEOUT_SEC_LOGIN,
                    'content' => $request
                ]
            ];
            $context = $this->_adapter->createContext($ctxOpts);
            $uri = $this->_authBaseUrl . '/api/auth';
            $contents = $this->_adapter->getContents($uri, $context);
            if ($contents === false) {
                $msg = "Cannot log in to Odoo REST API. " . $this->_traceConnectionData();
                $this->_logger->critical($msg);
                throw new \Exception($msg);
            }
            $response = $this->_adapter->decodeJson($contents);
            $respData = $this->_manObj->create(DataObject::class, ['arg1' => $response]);
            $this->_cachedOdooUserId = $respData->getData(self::ODOO_PATH_USER_ID);
            $this->_cachedOdooSessionId = $respData->getData(self::ODOO_PATH_SESSION_ID);
            $msg = "Logged in to Odoo as user with id '{$this->_cachedOdooUserId}' using REST API. " . $this->_traceConnectionData();
            $this->_logger->info($msg);
        }
        return $this->_cachedOdooSessionId;
    }

    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        if (is_null($this->_cachedOdooUserId)) {
            /* request User ID from Odoo */
            $params = [$this->_authDb, $this->_authUser, $this->_authPasswd];
            $outOpts = ['encoding' => 'utf-8', 'escaping' => 'markup'];
            $request = $this->_adapter->encodeXml('login', $params, $outOpts);
            $ctxOpts = [
                'http' => [
                    'method' => Rest::HTTP_METHOD_POST,
                    'header' => 'Content-Type: text/xml; charset=utf-8',
                    'timeout' => self::HTTP_TIMEOUT_SEC_LOGIN,
                    'content' => $request
                ]
            ];
            $context = $this->_adapter->createContext($ctxOpts);
            $uri = $this->_authBaseUrl . '/xmlrpc/common';
            $contents = $this->_adapter->getContents($uri, $context);
            if ($contents === false) {
                $msg = "Cannot log in to Odoo XML RPC API. " . $this->_traceConnectionData();
                $this->_logger->critical($msg);
                throw new \Exception($msg);
            }
            $this->_cachedOdooUserId = $this->_adapter->decodeXml($contents);
            $msg = "Logged in to Odoo as user with id '{$this->_cachedOdooUserId}' using XML RPC. " . $this->_traceConnectionData();
            $this->_logger->info($msg);
        }
        return $this->_cachedOdooUserId;
    }
}