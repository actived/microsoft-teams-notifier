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
use Monolog\DateTimeImmutable;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;

class MicrosoftTeamsRecordTest extends TestCase
{
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testCreate(): void
    {
        $title = 'Message Title';
        $subject = 'Message Subject';
        $record = new MicrosoftTeamsRecord($title, $subject);
        $this->assertInstanceOf(MicrosoftTeamsRecord::class, $record);
        $this->assertEquals('Message Title', $record->getTitle());
        $this->assertEquals('Message Subject', $record->getSubject());
    }

    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \PHPUnit\Framework\Exception
     */
    public function testData(): void
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
     * @return LogRecord Record
     */
    protected function getRecordData(Level $level = Level::Debug, string $message = 'log test', array $context = []): LogRecord
    {
        return new LogRecord(
            datetime: new DateTimeImmutable(true),
            channel: 'Test',
            level: $level,
            message: $message,
            context: $context,
            extra: [],
            formatted: 'Formatted message'
        );
    }
}