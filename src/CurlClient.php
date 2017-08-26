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
 * Http client with cURL
 * @package Sfn\HttpClient
 */
class CurlClient extends AbstractHttpClient
{
    /** @var resource $curl cURL handle */
    private $curl;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->curl = curl_init();
        $this->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->setOption(CURLOPT_HEADER, true);

        $this->setOption(CURLOPT_USERAGENT, $this->config['useragent']);
        $this->setOption(
            CURLOPT_FOLLOWLOCATION,
            $this->config['followlocation']
        );
        $this->setOption(CURLOPT_MAXREDIRS, $this->config['maxredirects']);
        $this->setOption(CURLOPT_TIMEOUT, $this->config['timeout']);
    }

    /**
     * {@inheritDoc}
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        $request = $this->setBaseUri($request);
        $request = $this->setDefaultHeaders($request);

        $this->setOption(CURLOPT_URL, (string) $request->getUri());
        $this->setOption(CURLOPT_CUSTOMREQUEST, $request->getMethod());
        $this->setOption(CURLOPT_POSTFIELDS, (string) $request->getBody());
        $this->setOption(
            CURLOPT_HTTPHEADER,
            $this->parseHeader($request->getHeaders())
        );

        $header = [];
        $this->setOption(
            CURLOPT_HEADERFUNCTION,
            function ($curl, $headerline) use (&$header) {
                if(!empty(trim($headerline)))
                    $header[] = trim($headerline);
                return strlen($headerline);
            }
        );

        $body = curl_exec($this->curl);

        if (curl_errno($this->curl)!=CURLE_OK) {
            throw new ConnectionException(
                curl_error($this->curl),
                curl_errno($this->curl),
                $request
            );
        }

        $status     = (int) curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $response = $this->config['responsefactory']->createResponse();
        $response = $response->withStatus($status);
        $response = $this->setResponseHeaders($response, $header);

        $response->getBody()->write($body);
        $response->getBody()->seek(0);

        $this->checkResponse($response, $request);

        return $response;
    }

    /**
     * Set cURL oprion
     * @param int $optname The CURLOPT_XXX option to set
     * @param mixed $optval Option value
     * @return bool
     */
    private function setOption(int $optname, $optval)
    {
        return curl_setopt($this->curl, $optname, $optval);
    }

    /**
     * Close cURL handle on object destruct
     */
    public function __destruct()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }
}
