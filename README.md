# Sfn HttpClient
[![MIT license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/sfn/psr7-httpclient/master/LICENSE)
[![PHP](https://img.shields.io/badge/PHP-7.0%2B-777bb4.svg)](http://www.php.net/)

Just a simple and little PSR7 client.
It is still a work-in-progress but, more or less, it works.

## Table of Contents
* [Requirements](#requirements)
* [Installation](#installation)
    * [Zend Diactoros support](#zend-diactoros-support)
    * [Guzzle support](#guzzle-support)
    * [Slim support](#slim-support)
* [Usage](#usage)
    * [Create a client instance](#create-a-client-instance)
    * [Send a request](#send-a-request)
    * [Methods for REST API](#helper-methods-for-rest-api)
    * [Base URI](#base-uri)
* [To-do](#to-do)

## Requirements
* PHP 7.0 or higher
* `php-curl` or `allow_url_fopen` set to true
* A [PSR7](http://www.php-fig.org/psr/psr-7/) implementation.

`Sfn\HttpClient` need a PSR-7 implementation. It supports
[Zend Diactoros](https://github.com/zendframework/zend-diactoros),
[Guzzle](https://github.com/guzzle/psr7) and
[Slim](https://github.com/slimphp/Slim-Http) at the moment.

Of course you can write your own HTTP Factory implementation for any PSR-7 implementation,
look [here](https://github.com/http-interop/http-factory-diactoros/tree/master/src)
for PSR-17 Http Factory interfaces.

## Installation
```
composer require sfn/httpclient
```

#### Zend Diactoros support
```
composer require http-interop/http-factory-diactoros
```

#### Guzzle support
```
composer require http-interop/http-factory-guzzle
```

#### Slim support
```
composer require http-interop/http-factory-slim
```

## Usage
### Create a client instance
With the `ClientFactory::make()` method you can create the correct instance
of the client. If it finds curl installed, it creates a client with a curl
backend, otherwise it create a client who send request via php's
`file_get_contents`.

`ClientFactory::make()` accepts an associative array with the client
configuration. You must specify at least your PSR-17 HTTP Factory implementation.
[Here](PARAMETERS.md#client-configuration-parameters) you can find the complete
list of parameters in you can set in the configuration array.

```php
// Zend Diactoros
$config = [
    'requestfactory'  => new Http\Factory\Diactoros\RequestFactory,
    'responsefactory' => new Http\Factory\Diactoros\ResponseFactory,
    'urifactory'      => new Http\Factory\Diactoros\UriFactory,
];

// Guzzle
$config = [
    'requestfactory'  => new Http\Factory\Guzzle\RequestFactory,
    'responsefactory' => new Http\Factory\Guzzle\ResponseFactory,
    'urifactory'      => new Http\Factory\Guzzle\UriFactory,
];

// Slim
$config = [
    'requestfactory'  => new Http\Factory\Slim\RequestFactory,
    'responsefactory' => new Http\Factory\Slim\ResponseFactory,
    'urifactory'      => new Http\Factory\Slim\UriFactory,
];

$client = Sfn\HttpClient\ClientFactory::make($config);
```

### Send a request
First of all, you must create a request with your preferred `Psr\Http\Message\RequestInterface` implementation. Then you simply call the
`send()` method of the client.
```php
$request = (new Zend\Diactoros\Request())
    ->withUri(new Zend\Diactoros\Uri('http://api.example.com/path'))
    ->withMethod('GET')
    ->withAddedHeader('Content-Type', 'application/json');

$response = $client->send($request); // Return a ResponseInterface
```

### Helper methods for REST API
There are `get()`, `post()`, `put()`, `delete()` and `patch()` helper methods.
You can pass a second parameter, with an array of options.
[Here](PARAMETERS.md#request-parameters) you can find a complete list of request parameters.

```php
// GET request
$response = $client->get('http://api.example.com/path');

// POST request
$response = $client->post(
    'http://api.example.com/path',
    ['body' => http_build_query(['foo' => 'bar'])]
);
```

### Base URI
You can also specify a base uri in the client configuration.
```php
$config = [
    'requestfactory'  => new Http\Factory\Diactoros\RequestFactory,
    'responsefactory' => new Http\Factory\Diactoros\ResponseFactory,
    'urifactory'      => new Http\Factory\Diactoros\UriFactory,
    'baseuri'         => 'http://api.example.com'
];
$client = Sfn\HttpClient\ClientFactory::make($config);

// GET request
$response = $client->get('path'); // GET http://api.example.com/path
```


## To-Do
* Cookies support
* SSL authentication
* Examples
* Better documentation
