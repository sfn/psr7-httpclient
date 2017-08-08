<?php
/**
 * Sfn HttpClient (https://github.com/sfn/psr7-httpclient)
 *
 * @author  Sfn (https://github.com/sfn)
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Sfn\HttpClient;

use Psr\Http\Message\UriInterface;
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
     * Send a GET request
     * @param string|UriInterface $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function get($uri, array $options=[]): ResponseInterface
    {
        return $this->sendRequest('GET', $uri, $options);
    }

    /**
     * Send a POST request
     * @param string|UriInterface $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function post($uri, array $options=[]): ResponseInterface
    {
        return $this->sendRequest('POST', $uri, $options);
    }

    /**
     * Send a PUT request
     * @param string|UriInterface $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function put($uri, array $options=[]): ResponseInterface
    {
        return $this->sendRequest('PUT', $uri, $options);
    }

    /**
     * Send a DELETE request
     * @param string|UriInterface $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function delete($uri, array $options=[]): ResponseInterface
    {
        return $this->sendRequest('DELETE', $uri, $options);
    }

    /**
     * Send a PATCH request
     * @param string|UriInterface $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function patch($uri, array $options=[]): ResponseInterface
    {
        return $this->sendRequest('PATCH', $uri, $options);
    }

    /**
     * Send a rquest
     * @param string $method HTTP Method
     * @param string|UriInterface $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function sendRequest(
        string $method,
        $uri,
        array $options=[]
    ): ResponseInterface
    {
        if (is_string($uri)) {
            $uri = new $this->config['uriclass']($uri);
        } elseif (!$uri instanceof UriInterface) {
            throw new \InvalidArgumentException(sprintf(
                'URI must be a string or a UriInterface instance; received "%s"',
                (is_object($uri) ? get_class($uri) : gettype($uri))
            ));
        }

        $request = (new $this->config['requestclass']())
            ->withMethod($method)
            ->withUri($uri);
        $request = $this->setRequestOptions($request, $options);

        return $this->send($request);
    }

    /**
     * Set request options
     * @param RequestInterface $request
     * @param array $options
     */
    private function setRequestOptions(
        RequestInterface $request,
        array $options=[]): RequestInterface
    {
        if (isset($options['body'])) {
            $request->getBody()->write($options['body']);
        }

        if (isset($options['headers']) && is_array($options['headers'])) {
            foreach ($options['headers'] as $header => $val) {
                $request = $request->withAddedHeader($header, $val);
            }
        }

        return $request;
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
