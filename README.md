# PSR7 HttpClient
Just a simple and little PSR7 client.
It is still a work-in-progress but, more or less, it works.

## Requirements
* PHP 7.0 or higher
* `php-curl` or `allow_url_fopen` set to true
* A [PSR7](http://www.php-fig.org/psr/psr-7/) implementation (eg. [zend-diactoros](https://github.com/zendframework/zend-diactoros))

## Usage
#### Create a client instance
With the `ClientFactory::make()` method you can create the correct instance
of the client. If it finds curl installed, it creates a client with a curl
backend, otherwise it create a client who send request via php's
`file_get_contents`.

`ClientFactory::make()` accepts an associative array with the client
configuration. You must specify at least a `Psr\Http\Message\ResponseInterface` implementation.

```php
$config = [
    'responseclass' => Zend\Diactoros\Response::class
];
$client = Sfn\HttpClient\ClientFactory::make($config);
```

#### Send a request
First of all, you must create a request with your preferred `Psr\Http\Message\RequestInterface` implementation. Then you simply call the
`send()` method of the client.
```php
$request = (new Zend\Diactoros\Request())
    ->withUri(new Zend\Diactoros\Uri('http://api.example.com/path'))
    ->withMethod('GET')
    ->withAddedHeader('Content-Type', 'application/json');

$response = $client->send($request); // Return a $config['responseclass'] object
```

#### Helper methods for REST API
There are `get()`, `post()`, `put()`, `delete()` and `patch()` helper methods.
In order to use those, you must specify in your client configuration also a
`Psr\Http\Message\RequestInterface` and a `Psr\Http\Message\UriInterface`
implementation.

```php
$config = [
    'responseclass' => Zend\Diactoros\Response::class,
    'requestclass'  => Zend\Diactoros\Request::class,
    'uriclass'      => Zend\Diactoros\Uri::class
];
$client = Sfn\HttpClient\ClientFactory::make($config);

// GET request
$response = $client->get('http://api.example.com/path');

// POST request
$response = $client->post(
    'http://api.example.com/path',
    ['body' => http_build_query(['foo' => 'bar'])]
);
```


## To-Do
* Cookies support
* SSL authentication
* Examples
* Better documentation
* Create a [composer](https://getcomposer.org/) package
