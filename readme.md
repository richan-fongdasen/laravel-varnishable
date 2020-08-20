[![Build Status](https://travis-ci.org/richan-fongdasen/laravel-varnishable.svg?branch=master)](https://travis-ci.org/richan-fongdasen/laravel-varnishable)
[![codecov](https://codecov.io/gh/richan-fongdasen/laravel-varnishable/branch/master/graph/badge.svg)](https://codecov.io/gh/richan-fongdasen/laravel-varnishable)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/richan-fongdasen/laravel-varnishable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/richan-fongdasen/laravel-varnishable/?branch=master)
[![StyleCI Analysis Status](https://github.styleci.io/repos/135787392/shield?branch=master)](https://github.styleci.io/repos/135787392)
[![Total Downloads](https://poser.pugx.org/richan-fongdasen/laravel-varnishable/d/total.svg)](https://packagist.org/packages/richan-fongdasen/laravel-varnishable)
[![Latest Stable Version](https://poser.pugx.org/richan-fongdasen/laravel-varnishable/v/stable.svg)](https://packagist.org/packages/richan-fongdasen/laravel-varnishable)
[![License: MIT](https://poser.pugx.org/laravel/framework/license.svg)](https://opensource.org/licenses/MIT)

# Laravel Varnishable

> Simple and easy varnish integration in Laravel

## Synopsis

This package offers easy ways to integrate your Laravel application with Varnish Cache.

## Table of contents

- [Setup](#setup)
- [Publish package assets](#publish-package-assets)
- [Configuration](#configuration)
- [Usage](#usage)
- [Credits](#credits)
- [License](#license)

## Setup

Install the package via Composer :

```sh
$ composer require richan-fongdasen/laravel-varnishable
```

### Laravel version compatibility

| Laravel version | Varnishable version |
| :-------------- | :------------------ |
| 5.1.x - 5.4.x   | 0.x                 |
| 5.5.x - 5.8.x   | 1.0.x - 1.1.x       |
| 6.x             | 1.2.x               |
| 7.x             | 1.3.x               |

> If you are using Laravel version 5.5+ then you can skip registering the service provider in your Laravel application.

### Service Provider

Add the package service provider in your `config/app.php`

```php
'providers' => [
    // ...
    RichanFongdasen\Varnishable\ServiceProvider::class,
];
```

### Alias

Add the package's alias in your `config/app.php`

```php
'aliases' => [
    // ...
    'Varnishable' => RichanFongdasen\Varnishable\Facade::class,
];
```

## Publish package assets

Publish the package asset files using this `php artisan` command

```sh
$ php artisan vendor:publish --provider="RichanFongdasen\Varnishable\ServiceProvider"
```

The command above would create new `varnishable.php` file in your application's config directory.

## Configuration

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Varnish hosts
    |--------------------------------------------------------------------------
    |
    | Specify the hostnames of your varnish instances. You can use array
    | to specify multiple varnish instances.
    |
    */
    'varnish_hosts' => env('VARNISH_HOST', '127.0.0.1'),

    /*
    |--------------------------------------------------------------------------
    | Varnish port
    |--------------------------------------------------------------------------
    |
    | Specify the port number that your varnish instances are listening to.
    |
    */
    'varnish_port' => env('VARNISH_PORT', 6081),

    /*
    |--------------------------------------------------------------------------
    | Cache duration
    |--------------------------------------------------------------------------
    |
    | Specify the default varnish cache duration in minutes.
    |
    */
    'cache_duration' => env('VARNISH_DURATION', 60 * 24),

    /*
    |--------------------------------------------------------------------------
    | Cacheable header
    |--------------------------------------------------------------------------
    |
    | Specify the custom HTTP header that we should add, so Varnish can
    | recognize any responses containing the header and cache them.
    |
    */
    'cacheable_header' => 'X-Varnish-Cacheable',

    /*
    |--------------------------------------------------------------------------
    | Uncacheable header
    |--------------------------------------------------------------------------
    |
    | Specify the custom HTTP header that we should add, so Varnish won't
    | cache any reponses containing this header.
    |
    */
    'uncacheable_header' => 'X-Varnish-Uncacheable',

    /*
    |--------------------------------------------------------------------------
    | Use ETag Header
    |--------------------------------------------------------------------------
    |
    | Please specify if you want to use ETag header for any of your static
    | contents.
    |
    */
    'use_etag' => true,

    /*
    |--------------------------------------------------------------------------
    | Use Last-Modified Header
    |--------------------------------------------------------------------------
    |
    | Please specify if you want to use Last-Modified header for any of your
    | static contents.
    |
    */
    'use_last_modified' => true,

    /*
    |--------------------------------------------------------------------------
    | ESI capability header
    |--------------------------------------------------------------------------
    |
    | Please specify the ESI capability header that the varnish server would
    | send if there is any ESI support.
    |
    */
    'esi_capability_header' => 'Surrogate-Capability',

    /*
    |--------------------------------------------------------------------------
    | ESI reply header
    |--------------------------------------------------------------------------
    |
    | Please specify the HTTP header that you want to send as a reply
    | in response to ESI capability header which the varnish server sent in
    | current request.
    |
    */
    'esi_reply_header' => 'Surrogate-Control',

];
```

## Usage

This section is currently under construction.

## Credits

- [spatie/laravel-varnish](https://github.com/spatie/laravel-varnish) - Some concepts in this repository was inspired by this package.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
