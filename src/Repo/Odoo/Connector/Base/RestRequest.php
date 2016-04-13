<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Base;

use Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\Def\Cover;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\ICover;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\ILogin;
use Praxigento\Odoo\Repo\Odoo\Connector\Config\IConnection;
use Psr\Log\LoggerInterface;

class RestRequest
{
    const DEF_TIMEOUT_SEC = 60;
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    /** @var  Adapter adapter for PHP functions to be mocked in tests */
    private $_adapter;
    /** @var  string */
    private $_baseUri;
    /** @var  LoggerInterface separate channel to log Odoo activity */
    private $_logger;
    /** @var  Login */
    private $_login;

    public function __construct(
        LoggerInterface $logger,
        Adapter $adapter,
        IConnection $params,
        ILogin $login
    ) {
        $this->_logger = $logger;
        $this->_adapter = $adapter;
        $this->_baseUri = $params->getBaseUri();
        $this->_login = $login;
    }

    /**
     * @param $params
     * @param $route
     * @param string $method
     * @param int $timeout
     * @return ICover
     * @throws \Exception
     */
    public function request($params, $route, $method = self::HTTP_METHOD_POST, $timeout = self::DEF_TIMEOUT_SEC)
    {
        /** @var string $sessId get session ID (authenticate if needed) */
        $sessId = $this->_login->getSessionId();
        /* encode request parameters as JSON string */
        $request = $this->_adapter->encodeJson($params);
        $contextOpts = [
            'http' => [
                'method' => $method,
                'header' => "Content-Type: application/json; charset=utf-8\r\nCookie: session_id=$sessId",
                'timeout' => $timeout,
                'content' => $request
            ]
        ];
        $context = $this->_adapter->createContext($contextOpts);
        $uri = $this->_baseUri . $route;
        $jsonContextOpt = $this->_adapter->encodeJson($contextOpts);
        $this->_logger->debug("Request URI:\t$uri");
        $this->_logger->debug("Request context:\t\n$jsonContextOpt");
        $contents = $this->_adapter->getContents($uri, $context);
        // $jsonHttpRespHeaders = $this->_adapter->encodeJson($http_response_header);
        if ($contents === false) {
            $msg = "Cannot request Odoo REST API ({$this->_baseUri}).";
            //$msg .= " HTTP Response headers: $jsonHttpRespHeaders";
            $this->_logger->critical($msg);
            throw new \Exception($msg);
        }
        $this->_logger->debug("Response:\t\n$contents");
        $data = $this->_adapter->decodeJson($contents);
        $result = new Cover($data);
        return $result;
    }
}