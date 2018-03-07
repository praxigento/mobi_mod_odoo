<?php
/**
 * Adapter for various PHP functions to mock its in tests (xmlrpc_encode_request, stream_context_create, ...).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Sub;

/**
 * @codeCoverageIgnore
 */
class Adapter
{
    /**
     * Create context for HTTP request.
     *
     * @param null $options
     * @param null $params
     * @return resource
     */
    public function createContext($options = null, $params = null)
    {
        $result = stream_context_create($options, $params);
        return $result;
    }

    /**
     * Decode JSON string as associative array.
     *
     * @param string $json
     * @return array
     */
    public function decodeJson($json)
    {
        $result = json_decode($json, true);
        return $result;
    }

    /**
     * @param string $xml
     * @return mixed
     */
    public function decodeXml($xml)
    {
        $result = xmlrpc_decode($xml, 'UTF-8');
        return $result;
    }

    /**
     * Encode array data to JSON string.
     *
     * @param array $params
     * @return string
     */
    public function encodeJson($params)
    {
        $result = json_encode($params);
        return $result;
    }

    /**
     * Encode parameters for XML RPC request.
     *
     * @param $method
     * @param $params
     * @param null $output_options
     * @return mixed
     */
    public function encodeXml($method, $params, $output_options = null)
    {
        $result = xmlrpc_encode_request($method, $params, $output_options);
        return $result;
    }

    /**
     * Get contents from URI.
     *
     * @param $uri
     * @param null $context
     * @return mixed
     */
    public function getContents($uri, $context = null)
    {
        $result = file_get_contents($uri, null, $context);
        return $result;
    }
}