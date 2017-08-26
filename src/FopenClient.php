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

/**
 * Http client via fopen
 * @package Sfn\HttpClient
 */
class FopenClient extends AbstractHttpClient
{
    /** @var array $httpContex */
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
        $request = $this->setBaseUri($request);
        $request = $this->setDefaultHeaders($request);

        $this->httpContex['http']['method']  = $request->getMethod();
        $this->httpContex['http']['content'] = (string) $request->getBody();
        $this->httpContex['http']['header']  =
            implode("\r\n", $this->parseHeader($request->getHeaders()));

        $contex = stream_context_create($this->httpContex);
        $handle = @fopen((string) $request->getUri(), 'r', false, $contex);

        if ($handle===false) {
            throw new ConnectionException('HTTP request failed', -1, $request);
        }

        $meta   = stream_get_meta_data($handle);
        $header = $meta['wrapper_data'];
        $body   = stream_get_contents($handle);
        fclose($handle);
        sscanf($meta['wrapper_data'][0], 'HTTP/%*d.%*d %d', $status);

        $response = $this->config['responsefactory']->createResponse();
        $response = $response->withStatus($status);
        $response = $this->setResponseHeaders($response, $header);

        $response->getBody()->write($body);
        $response->getBody()->seek(0);

        $this->checkResponse($response, $request);

        return $response;
    }
}
