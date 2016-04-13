<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector;

use Psr\Log\LoggerInterface;

class Rpc
{
    /** @var  Adapter adapter for PHP functions to be mocked in tests */
    private $_adapter;
    /** @var  LoggerInterface separate channel to log Odoo activity */
    private $_logger;
    /** @var  Login */
    private $_login;

    public function __construct(
        LoggerInterface $logger,
        Adapter $adapter,
        Login $login
    ) {
        $this->_logger = $logger;
        $this->_adapter = $adapter;
        $this->_login = $login;
    }

    public function request($resource, $operation, $params = null, $fields = null, $timeout = 60)
    {
        $file = null;
        $loginId = $this->_login->getUserId();
        $this->_logger->debug("Request for '$resource/$operation':\n" . var_export($params, true));
        /** compose RPC request parameters array */
        $rpcParams = [$this->_authDb, $loginId, $this->_authPasswd, $resource, $operation, $params];
        /** add attribute names array to filter result set */
        if (!is_null($fields)) {
            $rpcParams[] = $fields;
        }
        /** RPC options */
        $options = ['encoding' => 'utf-8', 'escaping' => 'markup'];
        $request = xmlrpc_encode_request('execute', $rpcParams, $options);
        $context = stream_context_create(
            [
                'http' => [
                    'method' => "POST",
                    'header' => "Content-Type: application/json; charset=utf-8",
                    'timeout' => $timeout,
                    'content' => $request
                ]
            ]
        );
        $file = file_get_contents($this->_authBaseUrl . '/xmlrpc/object', false, $context);
        if ($file === false) {
            Mage::throwException("Cannot execute '$resource/$operation' using OpenERP XML RPC.");
        }
        $result = xmlrpc_decode($file);
        $this->_logger->debug("Response for '$resource/$operation':\n" . var_export($result, true));
        return $result;
    }
}