<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\Def\Cover;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\ICover;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\ILogin;
use Praxigento\Odoo\Repo\Odoo\Connector\Config\IConnection;
use Praxigento\Odoo\Repo\Odoo\Connector\Sub\Adapter;
use Psr\Log\LoggerInterface;

class Rest
{
    const DEF_TIMEOUT_SEC = 60;
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    /** @var  Adapter adapter for PHP functions to be mocked in tests */
    protected $_adapter;
    /** @var  string */
    protected $_baseUri;
    /** @var  LoggerInterface separate channel to log Odoo activity */
    protected $_logger;
    /** @var  Login */
    protected $_login;
    /** @var ObjectManagerInterface */
    protected $_manObj;

    public function __construct(
        LoggerInterface $logger,
        ObjectManagerInterface $manObj,
        Adapter $adapter,
        IConnection $params,
        ILogin $login
    ) {
        $this->_logger = $logger;
        $this->_manObj = $manObj;
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
                'header' => "Content-Type: application/json\r\nCookie: session_id=$sessId",
                'timeout' => $timeout,
                'content' => $request
            ]
        ];
        $context = $this->_adapter->createContext($contextOpts);
        $uri = $this->_baseUri . $route;
        $this->_logger->debug("Request URI:\t$uri");
        $jsonContextOpt = $this->_adapter->encodeJson($contextOpts);
        $this->_logger->debug("Request context:\t\n$jsonContextOpt");
        $contents = $this->_adapter->getContents($uri, $context);
        if ($contents === false) {
            $msg = "Cannot request Odoo REST API ({$this->_baseUri}).";
            //$msg .= " HTTP Response headers: $jsonHttpRespHeaders";
            $this->_logger->critical($msg);
            throw new \Exception($msg);
        }
        $this->_logger->debug("Response:\t\n$contents");
        $data = $this->_adapter->decodeJson($contents);
        $result = $this->_manObj->create(Cover::class, ['data' => $data]);
        return $result;
    }
}