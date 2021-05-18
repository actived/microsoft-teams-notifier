Actived Microsoft Teams Notifier
================================

![Package Version](https://img.shields.io/badge/Version-1.0.1-brightgreen.svg)

A PHP package that defines custom Monolog handler to send Microsoft Teams notifications with Incoming Webhook.
The package aims to provide global messaging & log system that uses Microsoft Teams "MessageCard" notification and uses Monolog logging library.

# Features

- Monolog wiring with Microsoft Teams channel
- Application error notifying
- Simple messaging

# Install
```sh
$ composer require actived/microsoft-teams-notifier
```
# Microsoft Teams Webhook setting

Follow these steps to set up new Webhook:

- In Microsoft Teams, choose More options (⋯) next to the channel name and then choose 'Connectors'
- Find in the list of Connectors the 'Incoming Webhook' option, and choose 'Add'
- Provide required information for the new Webhook
- Copy the Webhook url - that information will be used to configure the package with `ACTIVED_MS_TEAMS_DSN`

# Symfony configuration

Place the code below in `.env` file:

```yaml
###> actived/microsoft-teams-notifier ###
ACTIVED_MS_TEAMS_DSN=webhook_dsn
ACTIVED_MS_TEAMS_TITLE=notification_title
###< actived/microsoft-teams-notifier ###
```

Register `ActivedMicrosoftTeamsHandler.php` as a new service with the code below:

```diff
// config\services.yaml

services:
    ...
    
    # ACTIVED MICROSOFT TEAMS NOTIFIER
+    actived_ms_teams_handler:
+        class: Actived\MicrosoftTeamsNotifier\Handler\MicrosoftTeamsHandler
+        arguments:
+            $webhookDsn: '%env(ACTIVED_MS_TEAMS_DSN)%'
+            $title: '%env(ACTIVED_MS_TEAMS_TITLE)%'
```

Modify your Monolog settings that will point from now to the new handler:

```diff
// config\packages\dev\monolog.yaml
// config\packages\prod\monolog.yaml

monolog:
    handlers:
        ...
        
        # ACTIVED MICROSOFT TEAMS NOTIFIER
+        teams:
+            type: service
+            id: actived_ms_teams_handler
```

# Laravel configuration

Place the code below in `.env` file:

```yaml
###> actived/microsoft-teams-notifier ###
ACTIVED_MS_TEAMS_DSN=webhook_dsn
ACTIVED_MS_TEAMS_TITLE=notification_title
###< actived/microsoft-teams-notifier ###
```

Modify your Monolog logging settings that will point to the new handler:

```diff
// config\logging.php

<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [
    'channels' => [
        'stack' => [
            'driver' => 'stack',
-            'channels' => ['single'],
+            'channels' => ['single', 'custom'],
            'ignore_exceptions' => false,
        ],
        
+       'custom' => [
+            'driver' => 'custom',
+            'via' => \Actived\MicrosoftTeamsNotifier\LogMonolog::class,
+            'path' => storage_path('logs/laravel.log'),
+            'url' => env('ACTIVED_MS_TEAMS_DSN'),
+            'title' => env('ACTIVED_MS_TEAMS_TITLE'),
+            'level' => env('LOG_LEVEL', 'debug'),
+        ],

...
```

# Usage

Correctly configured service in Symfony/Laravel will raise Logs in Microsoft Teams automatically accordingly to level assigned to.

### Symfony - manual messaging
```php
// LoggerInterface $logger
$logger->info('Info message with custom Handler');
$logger->error('Error message with custom Handler');
```

### Laravel - manual messaging
```php
// Illuminate\Support\Facades\Log
Log::channel('custom')->info('Info message with custom Handler');
Log::channel('custom')->error('Error message with custom Handler');
```

# License
The code is available under the MIT license. See the LICENSE file for more info.

