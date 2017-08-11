<?php
/**
 * Sfn HttpClient (https://github.com/sfn/psr7-httpclient)
 *
 * @author  Sfn (https://github.com/sfn)
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace Sfn\HttpClient;
use Psr\Http\Message\UriInterface;

/**
 * Uri helper functions
 * @package Sfn\HttpClient
 */
class UriHelper
{
    /**
     * Merge two URI
     * @param UriInterface $base
     * @param UriInterface $add
     * @return UriInterface Merged URI
     */
    public static function merge(UriInterface $base, UriInterface  $add): UriInterface
    {
        $mergeduri = $base;

        if ($add->getScheme()!='') {
            $mergeduri = $add;
        }
        else {
            $path = rtrim($base->getPath(), '/');
            $path .= '/'.$add->getPath();
            $mergeduri = $mergeduri->withPath($path);

            if ($add->getQuery()!='')
                $mergeduri = $mergeduri->withQuery($add->getQuery());
            if ($add->getFragment()!='')
                $mergeduri = $mergeduri->withFragment($add->getFragment());
        }

        return $mergeduri;
    }
}
