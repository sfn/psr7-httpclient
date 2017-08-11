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
        if (isset($this->config['baseuri'])) {
            $uri = UriHelper::merge(
                $this->config['baseuri'],
                $request->getUri()
            );
        }
        else {
            $uri = $request->getUri();
        }
        
        $this->setOption(CURLOPT_URL, (string) $uri);
        $this->setOption(CURLOPT_CUSTOMREQUEST, $request->getMethod());
        $this->setOption(CURLOPT_POSTFIELDS, (string) $request->getBody());
        $this->setOption(CURLINFO_HEADER_OUT, true);
        $this->setOption(
            CURLOPT_HTTPHEADER,
            $this->parseHeader($request->getHeaders())
        );

        $res = curl_exec($this->curl);

        if (curl_errno($this->curl)!=CURLE_OK) {
            throw new ConnectionException(
                curl_error($this->curl),
                curl_errno($this->curl),
                $request
            );
        }

        $info     = curl_getinfo($this->curl);
        $header   = substr($res, 0, $info['header_size']);
        $body     = substr($res, strlen($header));
        $header   = preg_split("/\\r\\n|\\r|\\n/", trim($header));

        $response = new $this->config['responseclass']();
        $response = $response->withStatus($info['http_code']);

        $lines = count($header);
        for ($i=1; $i<$lines; $i++) {
            $tmp = explode(':', $header[$i], 2);
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

}
