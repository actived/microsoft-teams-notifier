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

use Actived\MicrosoftTeamsNotifier\Handler\MicrosoftTeamsRecord;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Actived\MicrosoftTeamsNotifier\Handler\MicrosoftTeamsHandler;

class MicrosoftTeamsHandlerTest extends TestCase {

    /**
     * @var string
     */
    private string $webhookDsn;

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \RuntimeException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $envDsn = getenv('TEST_WEBHOOK_DSN');

        if (!$envDsn) {
            throw new \RuntimeException('TEST_WEBHOOK_DSN env variable not found!');
        }

        $this->webhookDsn = $envDsn;
    }

    /**
     * @throws void
     */
    private function createHandler(): MicrosoftTeamsHandler
    {
        return new MicrosoftTeamsHandler($this->webhookDsn, Level::Debug);
    }

    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testHandler(): void
    {
        $handler = $this->createHandler();
        $this->assertInstanceOf(MicrosoftTeamsHandler::class, $handler);
        $this->assertEquals('https://webhook2/uuid@uuid/IncomingWebhook/id/uuid', $handler->getWebhookDsn());
        $this->assertInstanceOf(MicrosoftTeamsRecord::class, $handler->getMicrosoftTeamsRecord());
        $this->assertEquals('Message', $handler->getMicrosoftTeamsRecord()->getTitle());
    }
}