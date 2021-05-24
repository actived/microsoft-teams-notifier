Actived Microsoft Teams Notifier
================================

![Package Version](https://img.shields.io/badge/Version-1.0.2-brightgreen.svg)

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
+            $title: 'Message title' 
+            $emoji:  '&#x1F6A8'  
+            $color: '#fd0404' 
+            $format: '[%datetime%] %channel%.%level_name%: %message%'
+            $level: 'error'
```
> *$webhookDsn:*  
> Microsoft Teams webhook url
> 
> *$title:*  
> title of Microsoft Teams Message
> 
> *$emoji:*  
> emoji of Microsoft Teams Message (dsiplayed next to the mlessage title). Value needs to reflect the mattern: ‘&#x<EMOJI_HEX_CODE>’
> 
> *$color:*  
> hexadecimal color value for Message Card color theme
> 
> *$color:*  
> every handler uses a Formatter to format the record before logging it. This attribute can be set to overwrite default log message (available options: %datetime% | %extra.token% | %channel% | %level_name% | %message%).
> 
> *$level:*  
> the minimum level for handler to be triggered and the message be logged in the channel (Monolog/Logger class: ‘error’ = 400)

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
‘type’: handler type (in our case this references custom notifier service)
‘id’: notifier service class \Actived\MicrosoftTeamsNotifier\LogMonolog

# Laravel configuration

Place the code below in `.env` file:

```yaml
###> actived/microsoft-teams-notifier ###
ACTIVED_MS_TEAMS_DSN=webhook_dsn
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
            'ignore_exceptions' => false
        ],
        
+       'custom' => [
+            'driver' => 'custom',
+            'via' => \Actived\MicrosoftTeamsNotifier\LogMonolog::class,
+            'webhookDsn' => env('ACTIVED_MS_TEAMS_DSN'),
+            'title'  => 'Message Title',
+            'emoji'  => '&#x1F3C1',
+            'color'  => '#fd0404',
+            'format' => '[%datetime%] %channel%.%level_name%: %message%'
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
> *title:*  
> title of Microsoft Teams Message
> 
> *level:*  
> the minimum level for handler to be triggered and the message be logged in the channel (Monolog/Logger class: ‘debug’ = 100)
>
> *emoji:*  
> emoji of Microsoft Teams Message (dsiplayed next to the mlessage title). Value needs to reflect the mattern: ‘&#x<EMOJI_HEX_CODE>’

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

