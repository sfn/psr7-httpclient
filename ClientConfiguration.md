# Client's configuration parameters
Here is the list of parameters you can set when defining e new `HttpClient`

| Name               | Type    |  Description             | Required    |
| ------------------ | ------- | ------------------------ | :---------: |
| `responseclass`    | class   | An implementation of `ResponseInterface` | YES |
| `requestlass`      | class   | An implementation of `RequestInterface`  | YES |
| `uriclass`         | class   | An implementation of `UriInterface`      | YES |
| `baseuri`          | string&#124;UriInterface | Base uri for requests | |
| `useragent`        | string  | Client's User-Agent |    |
| `followlocation`   | bool    | Follow HTTP 3xx redirect (Default: `false`) | |
| `maxredirects`     | integer | The maximum amount of HTTP redirections to follow (default: `0`) |      |
| `timeout`          | integer | Connection timeout in seconds (Default: php.ini's `default_socket_timeout`)| ||
