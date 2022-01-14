Actived Microsoft Teams Notifier
================================

![Package Version](https://img.shields.io/badge/Version-1.2.0-brightgreen.svg)

A PHP package that defines custom Monolog handler to send Microsoft Teams notifications with an Incoming Webhook.
The package aims to provide global messaging & log system that uses Microsoft Teams "MessageCard" notification and uses Monolog logging library.

# Features

- Monolog wiring with Microsoft Teams channel
- Application error notifying
- Simple messaging

# Install
```sh
$ composer require actived/microsoft-teams-notifier
```

### Since version 1.1 of the package, `"symfony/monolog-bundle"` was removed and replaced with `"monolog/monolog"` dependency that makes Actived Microsoft Teams Notifier more flexible for global use:

Please consider running `composer suggest` command to install required and missing dependencies related to framework you use (ex. Symfony):

```sh
$ composer suggest
actived/microsoft-teams-notifier suggests:
 - symfony/monolog-bundle: The MonologBundle provides integration of the Monolog library into the Symfony framework.
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
+            $level: 'error'
+            $title: 'Message title' 
+            $subject: 'Message subject' 
+            $emoji:  '&#x1F6A8'  
+            $color: '#fd0404' 
+            $format: '[%%datetime%%] %%channel%%.%%level_name%%: %%message%%'
```
> *$webhookDsn:*  
> Microsoft Teams webhook url
>
> *$level:*  
> the minimum level for handler to be triggered and the message be logged in the channel (Monolog/Logger class: ‘error’ = 400)
> 
> *$title (nullable):*  
> title of Microsoft Teams Message
> 
> *$subject (nullable):*  
> subject of Microsoft Teams Message
>
> *$emoji (nullable):*  
> emoji of Microsoft Teams Message (displayed next to the message title). Value needs to reflect the pattern: ‘&#x<EMOJI_HEX_CODE>’
> 
> *$color (nullable):*  
> hexadecimal color value for Message Card color theme
> 
> *$format (nullable):*  
> every handler uses a Formatter to format the record before logging it. This attribute can be set to overwrite default log message (available options: %datetime% | %extra.token% | %channel% | %level_name% | %message%).

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

> *type:*  
> handler type (in our case this references custom notifier service)
>
> *id:*  
> notifier service class \Actived\MicrosoftTeamsNotifier\LogMonolog

# Laravel configuration

Place the code below in `.env` file:

```yaml
###> actived/microsoft-teams-notifier ###
ACTIVED_MS_TEAMS_DSN=webhook_dsn
###< actived/microsoft-teams-notifier ###
```

Modify your Monolog logging settings that will point to the new handler:

### Att: definition of ALL parameters is compulsory - please use NULL value for attributes you want to skip.

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
            'ignore_exceptions' => false
        ],

         # ACTIVED MICROSOFT TEAMS NOTIFIER
+       'custom' => [
+            'driver' => 'custom',
+            'via' => \Actived\MicrosoftTeamsNotifier\LogMonolog::class,
+            'webhookDsn' => env('ACTIVED_MS_TEAMS_DSN'),
+            'level'  => env('LOG_LEVEL', 'debug'), // or simply 'debug'
+            'title'  => 'Message Title', // can be NULL
+            'subject'  => 'Message Subject', // can be NULL
+            'emoji'  => '&#x1F3C1', // can be NULL
+            'color'  => '#fd0404', // can be NULL
+            'format' => '[%datetime%] %channel%.%level_name%: %message%' // can be NULL
+        ],

...
```
> *driver:*  
> is a crucial part of each channel that defines how and where the log message is recorded. The ‘custom’ driver calls a specified factory to create a channel.
>
> *via:*  
> factory class which will be invoked to create the Monolog instance
> 
> *webhookDsn:*  
> Microsoft Teams webhook url
> 
> *level:*  
> the minimum level for handler to be triggered and the message be logged in the channel (Monolog/Logger class: ‘debug’ = 100)
>
> *title (nullable):*  
> title of Microsoft Teams Message
> 
> *subject (nullable):*  
> subject of Microsoft Teams Message
>
> *emoji (nullable):*  
> emoji of Microsoft Teams Message (displayed next to the message title). Value needs to reflect the pattern: ‘&#x<EMOJI_HEX_CODE>’
>
> *color (nullable):*  
> hexadecimal color value for Message Card color theme
>
> *format (nullable):*  
> message template - available options: %datetime% | %extra.token% | %channel% | %level_name% | %message%


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

