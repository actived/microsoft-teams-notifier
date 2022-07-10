<?php

/*
 * This file is part of the Actived/microsoft-teams-notifier
 *
 * Copyright (c) 2021 Actived
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Actived\MicrosoftTeamsNotifier;

use Actived\MicrosoftTeamsNotifier\Handler\MicrosoftTeamsHandler;
use Monolog\Logger;

class LogMonolog
{
    /**
     * @param array $config
     * @return Logger
     */
    public function __invoke(array $config): Logger
    {
        return new Logger(
            $config['title'],
            [new MicrosoftTeamsHandler(
                    $config['webhookDsn'],
                    $config['level'],
                    $config['title'],
                    $config['subject'],
                    $config['emoji'],
                    $config['color'],
                    $config['format']
                )
            ]
        );
    }
}
