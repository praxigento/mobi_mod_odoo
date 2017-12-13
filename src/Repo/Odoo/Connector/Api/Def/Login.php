<?php
/**
 * Implementation of the login operation to get UserId for Odoo API requests.
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Api\Def;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Repo\Odoo\Connector\Rest;
use Praxigento\Odoo\Repo\Odoo\Connector\Sub\Adapter;

class Login
    implements \Praxigento\Core\App\ICached, \Praxigento\Odoo\Repo\Odoo\Connector\Api\ILogin
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
    /**#@- */

    /** @var  Adapter adapter for PHP functions to be mocked in tests */
    protected $adapter;
    /**
     * Odoo connection data
     */
    protected $authBaseUrl;
    protected $authDb;
    protected $authPasswd;
    protected $authUser;
    /** @var int cache for Session ID of the authenticated Odoo REST API user. */
    protected $cachedOdooSessionId = null;
    /** @var int cache for User ID of the authenticated Odoo XML RPC API user. */
    protected $cachedOdooUserId = null;
    /** @var  \Psr\Log\LoggerInterface separate channel to log Odoo activity */
    protected $logger;
    /** @var ObjectManagerInterface */
    protected $manObj;

    function __construct(
        \Psr\Log\LoggerInterface $logger,
        ObjectManagerInterface $manObj,
        Adapter $adapter,
        \Praxigento\Odoo\Helper\Config $hlpConfig
    ) {
        $this->logger = $logger;
        $this->manObj = $manObj;
        $this->adapter = $adapter;
        $this->authBaseUrl = $hlpConfig->getConnectUri();
        $this->authDb = $hlpConfig->getConnectDb();
        $this->authUser = $hlpConfig->getConnectUser();
        $this->authPasswd = $hlpConfig->getConnectPassword();
    }

    public function cacheReset()
    {
        $this->cachedOdooUserId = null;
        $this->cachedOdooSessionId = null;
    }

    public function getSessionId()
    {
        if (is_null($this->cachedOdooSessionId)) {
            /* request User ID from Odoo */
            $params = [
                self::ODOO_DB_NAME => $this->authDb,
                self::ODOO_LOGIN => $this->authUser,
                self::ODOO_PASSWORD => $this->authPasswd
            ];
            $request = $this->adapter->encodeJson($params);
            $ctxOpts = [
                'http' => [
                    'method' => Rest::HTTP_METHOD_POST,
                    'header' => 'Content-Type: application/json; charset=utf-8',
                    'timeout' => self::HTTP_TIMEOUT_SEC_LOGIN,
                    'content' => $request
                ]
            ];
            $context = $this->adapter->createContext($ctxOpts);
            $uri = $this->authBaseUrl . '/api/auth';
            $contents = $this->adapter->getContents($uri, $context);
            if ($contents === false) {
                $msg = "Cannot log in to Odoo REST API. " . $this->traceConnectionData();
                $this->logger->critical($msg);
                throw new \Exception($msg);
            }
            $response = $this->adapter->decodeJson($contents);
            $respData = new \Praxigento\Core\Data($response);
            $this->cachedOdooUserId = $respData->get(self::ODOO_PATH_USER_ID);
            if ($this->cachedOdooUserId) {
                $this->cachedOdooSessionId = $respData->get(self::ODOO_PATH_SESSION_ID);
                $msg = "Logged in to Odoo as user with id '{$this->cachedOdooUserId}' using REST API. " . $this->traceConnectionData();
                $this->logger->info($msg);
            } else {
                $this->cachedOdooSessionId = null;
                $msg = "Cannot be authenticated in Odoo with username '{$this->authUser}'.";
                $this->logger->error($msg);
                $this->logger->error("Response from Odoo: " . $contents);
                throw new \Exception($msg);
            }
        }
        return $this->cachedOdooSessionId;
    }

    public function getUserId()
    {
        if (is_null($this->cachedOdooUserId)) {
            /* request User ID from Odoo */
            $params = [$this->authDb, $this->authUser, $this->authPasswd];
            $outOpts = ['encoding' => 'utf-8', 'escaping' => 'markup'];
            $request = $this->adapter->encodeXml('login', $params, $outOpts);
            $ctxOpts = [
                'http' => [
                    'method' => Rest::HTTP_METHOD_POST,
                    'header' => 'Content-Type: text/xml; charset=utf-8',
                    'timeout' => self::HTTP_TIMEOUT_SEC_LOGIN,
                    'content' => $request
                ]
            ];
            $context = $this->adapter->createContext($ctxOpts);
            $uri = $this->authBaseUrl . '/xmlrpc/common';
            $contents = $this->adapter->getContents($uri, $context);
            if ($contents === false) {
                $msg = "Cannot log in to Odoo XML RPC API. " . $this->traceConnectionData();
                $this->logger->critical($msg);
                throw new \Exception($msg);
            }
            $this->cachedOdooUserId = $this->adapter->decodeXml($contents);
            $msg = "Logged in to Odoo as user with id '{$this->cachedOdooUserId}' using XML RPC. " . $this->traceConnectionData();
            $this->logger->info($msg);
        }
        return $this->cachedOdooUserId;
    }

    /**
     * Short method to get connection data to trace into log.
     *
     * @return string
     */
    private function traceConnectionData()
    {
        $result = "Odoo connection data (url/db/user): {$this->authBaseUrl} / {$this->authDb} / {$this->authUser}.";
        return $result;
    }
}