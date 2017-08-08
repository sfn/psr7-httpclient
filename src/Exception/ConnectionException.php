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
    private $request;

    public function __construct(
        $message,
        RequestInterface $request,
        \Exception $previous = null
    ) {
        $this->request = $request;
        parent::__construct($message, 0, $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest() : RequestInterface
    {
        return $this->request;
    }
}
