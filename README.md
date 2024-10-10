# PHP Redmine API

[![Latest Version](https://img.shields.io/github/release/kbsali/php-redmine-api.svg)](https://github.com/kbsali/php-redmine-api/releases)
[![Software License](https://img.shields.io/badge/license-MIT-blueviolet.svg)](LICENSE)
[![Build Status](https://github.com/kbsali/php-redmine-api/actions/workflows/tests.yml/badge.svg?branch=v2.x)](https://github.com/kbsali/php-redmine-api/actions)
[![Codecov](https://codecov.io/gh/kbsali/php-redmine-api/graph/badge.svg?token=4P6dAaDbJ9)](https://codecov.io/gh/kbsali/php-redmine-api)
[![Total Downloads](https://img.shields.io/packagist/dt/kbsali/redmine-api.svg)](https://packagist.org/packages/kbsali/redmine-api)

A simple PHP Object Oriented wrapper for Redmine API.

Uses [Redmine API](https://www.redmine.org/projects/redmine/wiki/Rest_api/).

## Features

* Follows PSR-4 conventions and coding standard: autoload friendly
* Choose between using native `cURL` function or any
[PSR-18 HTTP client implementation ](https://packagist.org/providers/psr/http-client-implementation)
like [Guzzle](https://github.com/guzzle/guzzle) for handling http connections
* [mid-level API](docs/usage.md#mid-level-api) e.g.
    ```php
    $client->getApi('issue')->create(['project_id' => 1, 'subject' => 'issue title']);
    ```
* [low-level API](docs/usage.md#low-level-api) e.g.
    ```php
    $response = $client->request(
        HttpFactory::makeJsonRequest(
            'POST',
            '/issues.json',
            '{"issue":{"project_id":1,"subject":"issue title"}}',
        ),
    );
    ```

## Supported Redmine versions

We support (and run tests against) the [latest supported Redmine versions](https://www.redmine.org/projects/redmine/wiki/Download#Versions-status-and-releases-policy)
that receive security updates.

- Redmine 5.1.x
- Redmine 5.0.x
- Redmine 4.2.x

Nevertheless, you can also use this library for all older Redmine versions.
In this case, however, be aware that some features might not be supported by your Redmine server.

If a new Redmine version enables new features that are not yet supported with this library,
you are welcome to [create an issue](https://github.com/kbsali/php-redmine-api/issues).

## Requirements

* PHP ^7.4 || ^8.0
* The PHP [SimpleXML](https://php.net/manual/en/book.simplexml.php) extension
* The PHP [JSON](https://php.net/manual/en/book.json.php) extension
* Enabled REST web service on your Redmine server
    * Go to Administration -> Settings -> Api (`/settings/edit?tab=api`) and check the "Enable REST web service" box
    * Obtain your *API access key* in your profile page: `/my/account`
    * (or use your *username & password*; not recommended)

### Optional

* The PHP [cURL](https://php.net/manual/en/book.curl.php) extension if you want to use the native `cURL` functions.
* [PHPUnit](https://phpunit.de/) >= 9.0 (optional) to run the test suite

## Todo

* Tracking of Redmine API feature support in [#305](https://github.com/kbsali/php-redmine-api/issues/305)
* Check header's response code (especially for POST/PUT/DELETE requests)
    * See https://stackoverflow.com/questions/9183178/php-curl-retrieving-response-headers-and-body-in-a-single-request/9183272#9183272

## Limitations / Missing Redmine-API

Redmine is missing some APIs for a full remote management of the data:

* List of activities & roles: https://www.redmine.org/issues/11464
* [Open issues because of missing Redmine API](https://github.com/kbsali/php-redmine-api/labels/pending%3A%20missing%20api)

## Install

By using [Composer](https://getcomposer.org/) you can simply run:

```bash
$ php composer.phar require kbsali/redmine-api
```

at the root of your projects. To utilize the library, include
Composer's `vendor/autoload.php` in the scripts that will use the
`Redmine` classes.

For example,

```php
<?php
// This file is generated by Composer
require_once 'vendor/autoload.php';

$client = new \Redmine\Client\NativeCurlClient('https://redmine.example.com', '0ef19567656532f8dd43a4dbfeda787f01f3e659');
```

For a manual installation please follow this [instruction](https://github.com/kbsali/php-redmine-api/pull/285#issuecomment-856609296).

### Running the test suite

You can run test suite to make sure the library will work properly on your system. Simply run `vendor/bin/phpunit` in the project's directory :

```
$ vendor/bin/phpunit
PHPUnit 9.5.4 by Sebastian Bergmann and contributors.

Warning:       No code coverage driver available

...............................................................  63 / 432 ( 14%)
............................................................... 126 / 432 ( 29%)
............................................................... 189 / 432 ( 43%)
............................................................... 252 / 432 ( 58%)
............................................................... 315 / 432 ( 72%)
............................................................... 378 / 432 ( 87%)
......................................................          432 / 432 (100%)

Time: 00:00.149, Memory: 14.00 MB

OK (432 tests, 1098 assertions)
```

## Basic usage of `php-redmine-api` client

### Start your project

Create your project e.g. in the `index.php` by require the `vendor/autoload.php` file.

```diff
+<?php
+
+require_once 'vendor/autoload.php';
```

### Instantiate a Redmine Client

You can choose between:

1. a native curl client or
2. the PSR-18 compatible client.

#### 1. Native curl Client `Redmine\Client\NativeCurlClient`

> :bulb: This client was introduced in `php-redmine-api` v1.8.0. If you are
> using the old `Redmine\Client` please [see this migration guide for help to
> upgrade your code](docs/migrate-to-nativecurlclient.md).

You will need a URL to your Redmine instance and either a valid Apikey...

```diff
<?php

require_once 'vendor/autoload.php';
+
+// Instantiate with ApiKey
+$client = new \Redmine\Client\NativeCurlClient('https://redmine.example.com', '1234567890abcdfgh');
```

... or valid username/password.

```diff
<?php

require_once 'vendor/autoload.php';
+
+// Instantiate with Username/Password (not recommended)
+$client = new \Redmine\Client\NativeCurlClient('https://redmine.example.com', 'username', 'password');
```

> :bulb: For security reason it is recommended that you use an ApiKey rather than your username/password.

##### cURL configuration

After you instantiate a client you can set some optional `cURL` settings.

```diff
<?php

require_once 'vendor/autoload.php';

// Instantiate with ApiKey
$client = new Redmine\Client\NativeCurlClient('https://redmine.example.com', '1234567890abcdfgh');
+
+// [OPTIONAL] if you want to check the servers' SSL certificate on Curl call
+$client->setCurlOption(CURLOPT_SSL_VERIFYPEER, true);
+
+// [OPTIONAL] set the port (it will try to guess it from the url)
+$client->setCurlOption(CURLOPT_PORT, 8080);
+
+// [OPTIONAL] set a custom host
+$client->setCurlOption(CURLOPT_HTTPHEADER, ['Host: https://custom.example.com']);

```
#### 2. Psr-18 compatible Client `Redmine\Client\Psr18Client`

> :bulb: This client was introduced in `v1.7.0` of this library. If you are using the old `Redmine\Client` please [follow this migration guide](docs/migrate-to-psr18client.md).

The `Psr18Client` requires

- a `Psr\Http\Client\ClientInterface` implementation (like guzzlehttp/guzzle), [see](https://packagist.org/providers/psr/http-client-implementation)
- a `Psr\Http\Message\RequestFactoryInterface` implementation (like guzzlehttp/psr7), [see](https://packagist.org/providers/psr/http-factory-implementation)
- a `Psr\Http\Message\StreamFactoryInterface` implementation (like guzzlehttp/psr7), [see](https://packagist.org/providers/psr/http-message-implementation)
- a URL to your Redmine instance
- an Apikey or username
- and optional a password if you want tu use username/password.

> :bulb: For security reason it is recommended that you use an ApiKey rather than your username/password.

```diff
<?php

require_once 'vendor/autoload.php';
+
+$guzzle = new \GuzzleHttp\Client();
+$psr17Factory = new \GuzzleHttp\Psr7\HttpFactory();
+
+// Instantiate with ApiKey
+$client = new \Redmine\Client\Psr18Client(
+    $guzzle,
+    $psr17Factory,
+    $psr17Factory,
+    'https://redmine.example.com',
+    '1234567890abcdfgh'
+);
+// ...or Instantiate with Username/Password (not recommended)
+$client = new \Redmine\Client\Psr18Client(
+    $guzzle,
+    $psr17Factory,
+    $psr17Factory,
+    'https://redmine.example.com',
+    'username',
+    'password'
+);
```

##### Guzzle configuration

Because the `Psr18Client` is agnostic about the HTTP client implementation every configuration specific to the transport has to be set to the `Psr\Http\Client\ClientInterface` implementation.

This means that if you want to set any `cURL` settings to `Guzzle` you have multiple ways to set them:

1. Using [Guzzle environment variables](https://docs.guzzlephp.org/en/stable/quickstart.html#environment-variables)
2. Using [request options](https://docs.guzzlephp.org/en/stable/request-options.html) inside a `Psr\Http\Client\ClientInterface` wrapper:

```diff
<?php

require_once 'vendor/autoload.php';

+use Psr\Http\Client\ClientInterface;
+use Psr\Http\Message\RequestInterface;
+use Psr\Http\Message\ResponseInterface;
+
$guzzle = \GuzzleHttp\Client();
$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

+$guzzleWrapper = new class(\GuzzleHttp\Client $guzzle) implements ClientInterface
+{
+    private $guzzle;
+
+    public function __construct(\GuzzleHttp\Client $guzzle)
+    {
+        $this->guzzle = $guzzle;
+    }
+
+    public function sendRequest(RequestInterface $request): ResponseInterface
+    {
+        return $this->guzzle->send($request, [
+            // Set the options for every request here
+            'auth' => ['username', 'password', 'digest'],
+            'cert' => ['/path/server.pem', 'password'],
+            'connect_timeout' => 3.14,
+            // Set specific CURL options, see https://docs.guzzlephp.org/en/stable/faq.html#how-can-i-add-custom-curl-options
+            'curl' => [
+                CURLOPT_SSL_VERIFYPEER => 1,
+                CURLOPT_SSL_VERIFYHOST => 2,
+                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
+            ],
+        ]);
+    }
+};
+
// Instantiate with ApiKey
$client = new \Redmine\Client\Psr18Client(
-    $guzzle,
+    $guzzleWrapper,
    $psr17Factory,
    $psr17Factory,
    'https://redmine.example.com',
    '1234567890abcdfgh'
);
```

## Built-in Redmine features

### Impersonate User

Redmine allows you [to impersonate another user](https://www.redmine.org/projects/redmine/wiki/Rest_api#User-Impersonation). This can be done using the methods `startImpersonateUser()` and `stopImpersonateUser()`.

```php
$client->startImpersonateUser('kim');
// all requests will now impersonate the user `kim`

// To stop impersonation
$client->stopImpersonateUser();
```

### API usage

You can now use the `getApi()` method to create and get a specific Redmine API.

```php
<?php

$client->getApi('user')->list();
$client->getApi('user')->listLogins();

$client->getApi('issue')->create([
    'project_id'  => 'test',
    'subject'     => 'some subject',
    'description' => 'a long description blablabla',
    'assigned_to_id' => 123, // or 'assigned_to' => 'user1' OR 'groupXX'
]);
$client->getApi('issue')->list([
    'limit' => 1000
]);
```

[See further examples and read more about usage in the docs](docs/usage.md).

### Thanks!

* Thanks to [Thomas Spycher](https://github.com/tspycher/) for the 1st version of the class.
* Thanks to [Thibault Duplessis aka. ornicar](https://github.com/ornicar) for the php-github-api library, great source of inspiration!
* And all the [contributors](https://github.com/kbsali/php-redmine-api/graphs/contributors)
* specially [JanMalte](https://github.com/JanMalte) for his impressive contribution to the test coverage! :)
