<?php
/**
 * Sfn HttpClient (https://github.com/sfn/psr7-httpclient)
 *
 * @author  Sfn (https://github.com/sfn)
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Sfn\HttpClient;

/**
 * Factory method for RestClient
 * @package Sfn\HttpClient
 */
class ClientFactory {

    /**
     * Factory method for a new Http Client
     * @param array $config Associative array with client's configuration
     * @return CurlClient|FopenClient Client instance
     */
    public static function make(array $config)
    {
        if (function_exists('curl_version')) {
            $client = new CurlClient($config);
        } elseif (ini_get('allow_url_fopen')) {
            $client = new FopenClient($config);
        } else {
            throw new \RuntimeException(
                "You need php-curl installed or allow_url_fopen=true",
                0
            );
        }
        return $client;
    }
}
