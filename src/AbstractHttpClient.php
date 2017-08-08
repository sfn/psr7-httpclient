<?php
/**
 * Sfn HttpClient (https://github.com/sfn/psr7-httpclient)
 *
 * @author  Sfn (https://github.com/sfn)
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Sfn\HttpClient;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

abstract class AbstractHttpClient
{
    const VERSION = '0.1.0';
    protected $config;

    /**
     * Send a request
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    abstract public function send(RequestInterface $request): ResponseInterface;

    /**
     * A new client instance
     * @param array $config Associative array with client's configuration
     */
    public function __construct(array $config)
    {
        $this->config = [
            'useragent'      => 'Sfn-HttpClient/'.self::VERSION.' (https://github.com/sfn/psr7-httpclient)',
            'followlocation' => false,
            'maxredirects'   => 0,
            'timeout'        => ini_get('default_socket_timeout'),
        ];

        $this->config = array_merge($this->config, $config);

        if (
            !isset($this->config['responseclass']) ||
            !new $this->config['responseclass'] instanceof ResponseInterface
        ) {
            throw new \InvalidArgumentException(
                'You must specify a ResponseInterface implementation'
            );
        }
    }

    /**
     * Parse request's headers
     * @param array $headers
     * @return array
     */
    protected function parseHeader(array $headers){
        $temp = array();
        foreach ($headers as $key => $val) {
            $temp[] = $key.': '.implode('; ',$val);
        }
        return $temp;
    }
}
