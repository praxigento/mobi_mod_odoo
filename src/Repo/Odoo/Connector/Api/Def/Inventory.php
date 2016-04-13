<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Api\Def;

use Praxigento\Odoo\Repo\Odoo\Connector\Api\ILogin;
use Praxigento\Odoo\Repo\Odoo\Connector\Base\Adapter;
use Praxigento\Odoo\Repo\Odoo\Connector\Config\IConnection;
use Psr\Log\LoggerInterface;

class Inventory
{
    /** @var  Adapter adapter for PHP functions to be mocked in tests */
    private $_adapter;
    /** @var  string */
    private $_baseUri;
    /** @var  LoggerInterface separate channel to log Odoo activity */
    private $_logger;
    /** @var  ILogin */
    private $_login;

    public function __construct(
        LoggerInterface $logger,
        Adapter $adapter,
        IConnection $params,
        ILogin $login
    ) {
        $this->_logger = $logger;
        $this->_adapter = $adapter;
        $this->_login = $login;
        $this->_baseUri = $params->getBaseUri();
    }

    public function get()
    {
        $contents = null;
        $timeout = 15;
        $sessId = $this->_login->getSessionId();
//        $this->_logger->debug("Request for '$resource/$operation':\n" . var_export($params, true));
        /** compose RPC request parameters array */
//        $rpcParams = [$this->_authDb, $loginId, $this->_authPasswd, $resource, $operation, $params];
        $rpcParams = ['ids' => [428]];
        /** add attribute names array to filter result set */
//        if (!is_null($fields)) {
//            $rpcParams[] = $fields;
//        }
        /** RPC options */
        $options = ['encoding' => 'utf-8', 'escaping' => 'markup'];
        //$request = xmlrpc_encode_request('execute', $rpcParams, $options);
        $request = json_encode($rpcParams);
        $contextOpts = [
            'http' => [
                'method' => "POST",
                'header' => "Content-Type: application/json; charset=utf-8\r\nCookie: session_id=d8a82a8c31379cbd367b6186d275d6c0dfec0978\r\n",
                'timeout' => $timeout,
                'content' => $request
            ]
        ];
        $context = stream_context_create($contextOpts);
        $uri = $this->_baseUri . '/api/inventory/';
        $this->_logger->debug("Request URI:\t$uri");
        $this->_logger->debug("Request context:\t\n" . json_encode($contextOpts));
        $contents = $this->_adapter->getContents($uri, $context);
        if ($contents === false) {
            // Mage::throwException("Cannot execute '$resource/$operation' using OpenERP XML RPC.");
            2 + 2;
        }
        $this->_logger->debug("Response:\t\n$contents");
        $result = json_decode($contents);
        //$this->_logger->debug("Response for '$resource/$operation':\n" . var_export($result, true));
        return $result;
        1 + 1;
    }
}