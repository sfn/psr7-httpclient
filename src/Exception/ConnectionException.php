<?php
/**
 * Sfn HttpClient (https://github.com/sfn/psr7-httpclient)
 *
 * @author  Sfn (https://github.com/sfn)
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Sfn\HttpClient\Exception;

use Psr\Http\Message\RequestInterface;

class ConnectionException extends \Exception
{
    /** @var RequestInterface $request */
    private $request;

    /**
     * @param string $message
     * @param int $code
     * @param RequestInterface $request
     * @param \Exception|null $previous
     */
    public function __construct(
        $message,
        $code,
        RequestInterface $request,
        \Exception $previous = null
    ) {
        $this->request = $request;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest() : RequestInterface
    {
        return $this->request;
    }
}
