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
use Monolog\DateTimeImmutable;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class MicrosoftTeamsRecordTest extends TestCase {

    public function testCreate()
    {
        $title = 'Message Title';
        $subject = 'Message Subject';
        $record = new MicrosoftTeamsRecord($title, $subject);
        $this->assertInstanceOf(MicrosoftTeamsRecord::class, $record);
        $this->assertEquals('Message Title', $record->getTitle());
        $this->assertEquals('Message Subject', $record->getSubject());
    }

    public function testData()
    {
        $title = 'Message Title';
        $subject = 'Message Subject';
        $record = new MicrosoftTeamsRecord($title, $subject);
        $data = $record->setData($this->getRecordData())->getData();

        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('context', $data);
        $this->assertArrayHasKey('themeColor', $data);
        $this->assertArrayHasKey('title', $data);

        $this->assertSame(MicrosoftTeamsRecord::CARD_TYPE, $data['type']);
        $this->assertSame(MicrosoftTeamsRecord::CARD_CONTEXT, $data['context']);
    }

    /**
     * @return array Record
     */
    protected function getRecordData($level = Logger::DEBUG, $message = 'log test', array $context = []): array
    {
        return [
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'level_name' => Logger::getLevelName($level),
            'datetime' => new DateTimeImmutable(true),
            'extra' => [],
        ];
    }
}