<?php
/**
 * Sfn HttpClient (https://github.com/sfn/psr7-httpclient)
 *
 * @author  Sfn (https://github.com/sfn)
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Sfn\HttpClient\Exception;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseException extends \Exception
{
    /** @var RequestInterface $request */
    private $request;
    /** @var ResponseInterface $response */
    private $response;

    /**
     * @param string $message
     * @param int $code
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param \Exception|null $previous
     */
    public function __construct(
        $message,
        $code,
        RequestInterface $request,
        ResponseInterface $response,
        \Exception $previous = null
    ) {
        $this->request = $request;
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest() : RequestInterface
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse() : ResponseInterface
    {
        return $this->response;
    }
}
