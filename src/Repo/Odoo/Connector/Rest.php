<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector;

use Magento\Framework\ObjectManagerInterface;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\Def\Cover;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\Data\ICover;
use Praxigento\Odoo\Repo\Odoo\Connector\Api\ILogin;
use Praxigento\Odoo\Repo\Odoo\Connector\Sub\Adapter;
use Psr\Log\LoggerInterface;

class Rest
{
    const DEF_TIMEOUT_SEC = 60;
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    /** @var  Adapter adapter for PHP functions to be mocked in tests */
    protected $adapter;
    /** @var  string */
    protected $baseUri;
    /** @var  LoggerInterface separate channel to log Odoo activity */
    protected $logger;
    /** @var  Login */
    protected $login;
    /** @var ObjectManagerInterface */
    protected $manObj;

    public function __construct(
        LoggerInterface $logger,
        ObjectManagerInterface $manObj,
        Adapter $adapter,
        ILogin $login,
        \Praxigento\Odoo\Helper\Config $hlpConfig
    ) {
        $this->logger = $logger;
        $this->manObj = $manObj;
        $this->adapter = $adapter;
        $this->baseUri = $hlpConfig->getConnectUri();
        $this->login = $login;
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
        $sessId = $this->login->getSessionId();
        /* encode request parameters as JSON string */
        $request = $this->adapter->encodeJson($params);
        $contextOpts = [
            'http' => [
                'method' => $method,
                'header' => "Content-Type: application/json\r\nCookie: session_id=$sessId",
                'timeout' => $timeout,
                'content' => $request
            ]
        ];
        $context = $this->adapter->createContext($contextOpts);
        $uri = $this->baseUri . $route;
        $this->logger->debug("Request URI:\t$uri");
        $jsonContextOpt = $this->adapter->encodeJson($contextOpts);
        $this->logger->debug("Request context:\t\n$jsonContextOpt");
        $contents = $this->adapter->getContents($uri, $context);
        if ($contents === false) {
            $msg = "Cannot request Odoo REST API ({$this->baseUri}).";
            //$msg .= " HTTP Response headers: $jsonHttpRespHeaders";
            $this->logger->critical($msg);
            throw new \Exception($msg);
        }
        $this->logger->debug("Response:\t\n$contents");
        $data = $this->adapter->decodeJson($contents);
        $result = $this->manObj->create(Cover::class, ['data' => $data]);
        return $result;
    }
}