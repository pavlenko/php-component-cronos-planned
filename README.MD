## Requirements

The following versions of PHP are supported.

* PHP 7.1+

## Installation

To install, use composer:

```
php composer.phar require pe/component-cronos-planned
```

## Usage

Server usage

```php
<?php

namespace PE\Component\Cronos;

use PE\Component\Cronos\Core\ServerInterface;
use PE\Component\Cronos\Planned\Storage\StorageInterface;
use PE\Component\Cronos\Planned\PlannedModule;

// Initialize server
/* @var $server ServerInterface */
$server = '...';

// Create provider
/* @var $storage StorageInterface */
$storage = '...';

// Attach module
$module = new PlannedModule($storage);
$server->attachModule('planned', $module);
```

Client usage

```php
<?php

namespace PE\Component\Cronos;

use PE\Component\Cronos\Core\ClientInterface;
use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Planned\PlannedAPI;

// Initialize client
/* @var $client ClientInterface */
$client = '...';

// Create API instance
$api = new PlannedAPI($client);

// Initialize task object
/* @var $task TaskInterface */
$task = '...';

// Call for insert task on remote
$api->insertTask($task);// -> Inserted TaskInterface instance

// Call for update task on remote
$api->updateTask($task);// -> Updated TaskInterface instance

// Call for remove task on remote
$api->removeTask($task);// -> Deleted TaskInterface instance
```
