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
use Sfn\HttpClient\Exception\ConnectionException;
use Sfn\HttpClient\Exception\ClientException;
use Sfn\HttpClient\Exception\ServerException;

/**
 * Http client via fopen
 * @package Sfn\HttpClient
 */
class FopenClient extends AbstractHttpClient
{
    private $httpContex;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->httpContex['http']['ignore_errors'] = true;

        $this->httpContex['http']['user_agent'] = $this->config['useragent'];
        $this->httpContex['http']['follow_location'] =
            (int) $this->config['followlocation'];
        $this->httpContex['http']['max_redirects'] =
            $this->config['maxredirects'];
        $this->httpContex['http']['timeout'] = $this->config['timeout'];
    }

    /**
     * {@inheritDoc}
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $this->httpContex['http']['method']  = $request->getMethod();
        $this->httpContex['http']['content'] = (string) $request->getBody();
        $this->httpContex['http']['header']  =
            implode("\r\n", $this->parseHeader($request->getHeaders()));

        $contex = stream_context_create($this->httpContex);
        $body = @file_get_contents((string) $request->getUri(), false, $contex);
        if ($body===false) {
            throw new ConnectionException('HTTP request failed', $request);
        }
        sscanf($http_response_header[0], 'HTTP/%*d.%*d %d', $status);

        $response = new $this->config['responseclass']();
        $response = $response->withStatus($status);

        $lines = count($http_response_header);
        for ($i=1; $i<$lines; $i++) {
            $tmp = explode(':', $http_response_header[$i], 2);
            $response = $response->withAddedHeader($tmp[0], $tmp[1]);
        }

        $response->getBody()->write($body);

        if ($response->getStatusCode()>=400) {
            if($response->getStatusCode()<500) {
                $exceptionclass = ClientException::class;
            } else {
                $exceptionclass = ServerException::class;
            }
            throw new $exceptionclass(
                $response->getReasonPhrase(),
                $response->getStatusCode(),
                $request,
                $response
            );
        }

        return $response;
    }
}
