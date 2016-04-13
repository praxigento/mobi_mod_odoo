<?php
/**
 * Implementation of the login operation to get UserId for Odoo API requests.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\Odoo\Repo\Odoo\Connector\Api\Def;

use Flancer32\Lib\DataObject;
use Praxigento\Core\ICached;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\ILogin;
use Praxigento\Odoo\Repo\Odoo\Connector\Base\Adapter;
use Praxigento\Odoo\Repo\Odoo\Connector\Config\IAuthentication;
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
    private $_adapter;
    /**
     * OpenERP connection data
     */
    private $_authBaseUrl;
    private $_authDb;
    private $_authPasswd;
    private $_authUser;
    /** @var int cache for Session ID of the authenticated Odoo REST API user. */
    private $_cachedOdooSessionId = null;
    /** @var int cache for User ID of the authenticated Odoo XML RPC API user. */
    private $_cachedOdooUserId = null;
    /** @var  LoggerInterface separate channel to log Odoo activity */
    private $_logger;

    function __construct(
        LoggerInterface $logger,
        Adapter $adapter,
        IAuthentication $params
    ) {
        $this->_logger = $logger;
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
            $params = new DataObject([
                self::ODOO_DB_NAME => $this->_authDb,
                self::ODOO_LOGIN => $this->_authUser,
                self::ODOO_PASSWORD => $this->_authPasswd
            ]);
            $request = json_encode($params->getData());
            $ctxOpts = [
                'http' => [
                    'method' => 'POST',
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
            $response = json_decode($contents, true);
            $respData = new DataObject($response);
            $this->_cachedOdooUserId = $respData->getData(self::ODOO_PATH_USER_ID);
            $this->_cachedOdooSessionId = $respData->getData(self::ODOO_PATH_SESSION_ID);
            $msg = "Logged in to Odoo as user with id '{$this->_cachedOdooUserId}'. " . $this->_traceConnectionData();
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
            $request = $this->_adapter->encodeRequest('login', $params, $outOpts);
            $ctxOpts = [
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: text/xml; charset=utf-8',
                    'timeout' => self::HTTP_TIMEOUT_SEC_LOGIN,
                    'content' => $request
                ]
            ];
            $context = $this->_adapter->createContext($ctxOpts);
            $uri = $this->_authBaseUrl . '/xmlrpc/common';
            $file = $this->_adapter->getContents($uri, $context);
            if ($file === false) {
                $msg = "Cannot log in to Odoo XML RPC API. " . $this->_traceConnectionData();
                $this->_logger->critical($msg);
                throw new \Exception($msg);
            }
            $this->_cachedOdooUserId = xmlrpc_decode($file);
            $msg = "Logged in to Odoo as user with id '{$this->_cachedOdooUserId}'. " . $this->_traceConnectionData();
            $this->_logger->info($msg);
        }
        return $this->_cachedOdooUserId;
    }
}