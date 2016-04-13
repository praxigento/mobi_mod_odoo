<?php
/**
 * Adapter for various PHP functions to mock its in tests (xmlrpc_encode_request, stream_context_create, ...).
 *
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\Odoo\Repo\Odoo\Connector\Base;


class Adapter
{

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

    public function encodeXml($method, $params, $output_options = null)
    {
        $result = xmlrpc_encode_request($method, $params, $output_options);
        return $result;
    }

    public function getContents($uri, $context = null)
    {
        $result = file_get_contents($uri, null, $context);
        return $result;
    }
}