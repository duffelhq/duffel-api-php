# duffel/api

A PHP library for the [Duffel API](https://duffel.com/docs/api).

## Contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)
* [License](#license)

## Requirements

* PHP >= 7.4
* A Duffel API access token ([this quick start
guide](https://duffel.com/docs/guides/quick-start) will help you to create
one)

## Installation

To get started, simply require this library using
[Composer](https://getcomposer.org/). You will also need to install packages
which provide
[`psr/http-client-implementation`](https://packagist.org/providers/psr/http-client-implementation) and
[`psr/http-factory-implementation`](https://packagist.org/providers/psr/http-factory-implementation).

An installation could look like the following command.
```sh
$ composer require "duffel/api:dev-main" "guzzlehttp/guzzle:^7.4" "http-interop/http-factory-guzzle:^1.2
```

## Usage

A simple example of using this library (after successfully installing it) follows.

```php
use Duffel\Client;

$client = new Duffel\Client();
$client->setAccessToken(getenv('DUFFEL_ACCESS_TOKEN'));

$client->airports->list();
```

See the [`examples/`](./examples) directory for additional working examples.

## License

Duffel's PHP API Client library is licensed under the [MIT
license](https://opensource.org/licenses/MIT).
