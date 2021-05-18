<?php

/*
 * This file is part of the Actived/microsoft-teams-notifier
 *
 * Copyright (c) 2021 Actived
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Actived\MicrosoftTeamsNotifier\Tests;

use Actived\MicrosoftTeamsNotifier\Handler\MicrosoftTeamsRecord;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Actived\MicrosoftTeamsNotifier\Handler\MicrosoftTeamsHandler;

class MicrosoftTeamsHandlerTest extends TestCase {

    private $webhookDsn;

    public function setUp(): void
    {
        parent::setUp();

        if (!$this->webhookDsn = getenv('TEST_WEBHOOK_DSN')) {
            throw new \RuntimeException('TEST_WEBHOOK_DSN env variable not found!');
        }
    }

    private function createHandler(): MicrosoftTeamsHandler
    {
        return new MicrosoftTeamsHandler($this->webhookDsn, Logger::DEBUG);
    }

    public function testHandler(): void
    {
        $handler = $this->createHandler();
        $this->assertInstanceOf(MicrosoftTeamsHandler::class, $handler);
        $this->assertEquals('https://webhook2/uuid@uuid/IncomingWebhook/id/uuid', $handler->getWebhookDsn());
        $this->assertInstanceOf(MicrosoftTeamsRecord::class, $handler->getMicrosoftTeamsRecord());
        $this->assertEquals('Message', $handler->getMicrosoftTeamsRecord()->getTitle());
    }
}