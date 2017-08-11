# Client's configuration parameters
Here is the list of parameters you can set when defining e new `HttpClient`.

| Name               | Type    |  Description             | Default             | Required    |
| ------------------ | ------- | ------------------------ | ------------------- | :---------: |
| `responseclass`    | class   | An implementation of `ResponseInterface` |      | YES         |
| `requestlass`      | class   | An implementation of `RequestInterface` |      | YES         |
| `uriclass`         | class   | An implementation of `UriInterface` |      | YES         |
| `baseuri`          | string&#124;UriInterface | Base uri for requests | | |
| `useragent`        | string  | Client's User-Agent      | Sfn-HttpClient/0.1.0 (https://github.com/sfn/psr7-httpclient) |    |
| `followlocation`   | bool    | Follow HTTP 3xx redirect | false               |             |
| `maxredirects`     | integer | The maximum amount of HTTP redirections to follow | 0 |      |
| `timeout`          | integer | Connection timeout in seconds | php.ini's `default_socket_timeout`| ||
