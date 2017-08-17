# Parameters

## Client configuration parameters
Here is the list of parameters you can set when defining e new `HttpClient`

| Name               | Type    |  Description             | Required    |
| ------------------ | ------- | ------------------------ | :---------: |
| `responsefactory`  | ResponseFactoryInterface   | Response factory | YES |
| `requestfactory`   | RequestFactoryInterface    | Request factory  | YES |
| `urifactory`       | UriFactoryInterface        | Uri factory      | YES |
| `headers`          | array   | Associative array with default headers   | |
| `baseuri`          | string&#124;UriInterface | Base uri for requests | |
| `useragent`        | string  | Client's User-Agent |    |
| `followlocation`   | bool    | Follow HTTP 3xx redirect (Default: `false`) | |
| `maxredirects`     | integer | The maximum amount of HTTP redirections to follow (default: `0`) |      |
| `timeout`          | integer | Connection timeout in seconds (Default: php.ini's `default_socket_timeout`)| ||

### Example
```php
$config = [
    'requestfactory'  => new Http\Factory\Diactoros\RequestFactory,
    'responsefactory' => new Http\Factory\Diactoros\ResponseFactory,
    'urifactory'      => new Http\Factory\Diactoros\UriFactory,
    'baseuri'         => 'http://api.example.com',
    'timeout'         => 30,
    'headers'         => [
        'Content-Type' => 'application/json'
    ]
];

$client = Sfn\HttpClient\ClientFactory::make($config);
```

## Request parameters
Here is the list of parameters you can set when sending a request via REST
methods.

| Name       | Type   | Description                              |
| ---------- | -------| -----------------------------------------|
| `headers`  | array  | Associative array with request headers   |
| `body`     | string | Request body                             |

### Example
```php
$options = [
    'body'    => json_encode($post_fields),
    'headers' => [
        'Content-Type' => 'application/json'
    ]
];

$response = $client->post('http://api.example.com/path', $options);
```
