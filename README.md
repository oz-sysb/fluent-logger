# Fluent Logger PHP for ozv

[![Build Status](https://travis-ci.org/oz-sysb/fluent-logger.svg?branch=master)](https://travis-ci.org/oz-sysb/fluent-logger)
[![Coverage Status](https://coveralls.io/repos/github/oz-sysb/fluent-logger/badge.svg?branch=master)](https://coveralls.io/github/oz-sysb/fluent-logger?branch=master)
[![Total Downloads](https://poser.pugx.org/oz-sysb/fluent-logger/downloads)](https://packagist.org/packages/oz-sysb/fluent-logger)
[![Packagist](https://img.shields.io/packagist/v/oz-sysb/fluent-logger.svg)](https://packagist.org/packages/oz-sysb/fluent-logger)
[![License](https://poser.pugx.org/oz-sysb/fluent-logger/license)](https://packagist.org/packages/oz-sysb/fluent-logger)

## Requirements

- PHP 5.3 or higher

# Installation

Library is available on [Packagist](https://packagist.org/packages/oz-sysb/fluent-logger).

It's recommended that you use [Composer](https://getcomposer.org/) to install Slim.

```bash
$ composer require oz-sysb/fluent-logger:^1.0
```

# Backward Compatibility Changes

As of v1, all loggers but `FluentLogger` are removed.

[Monolog](https://github.com/Seldaek/monolog) is recommended in such use cases.

# Usage

## PHP side

```php
<?php

require_once __DIR__.'/vendor/autoload.php';

use \Fluent\Logger\FluentLogger as Client;
use \OzSysb\Logger\OzLogger;

// Always define first
OzLogger::setApplication('woodstock');
// init class.
$logger = new OzLogger(new Client('unix:///var/run/td-agent/td-agent.sock'));

// ... snip ...

// Describe every required part
$type = 'api-client';

// ... snip ...
$logger->info($type, 'Post to https://example.com/api/member, and post params id=100&key=value', __FUNCTION__, __CLASS__);
$logger->info($type, 'Response from https://example.com/api/member response body is {"status": "successed!"}', __FUNCTION__, __CLASS__);


// ... snip ...
$type = 'db-error;
$logger->error($type, 'DB Error : ERROR 1099 (HY000): Table 'super1' was locked with a READ lock and can't be updted', __FUNCTION__, __CLASS__);
```

## Fluentd side

Use `in_forward`.

```aconf
<source>
  @type forward
</source>
```

## License
MIT
