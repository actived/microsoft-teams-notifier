<?php declare(strict_types=1);

/*
 * This file is part of the Actived/microsoft-teams-notifier
 *
 * Copyright (c) 2021 Actived
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Actived\MicrosoftTeamsNotifier\Handler;

use Monolog\Logger;

class MicrosoftTeamsRecord {

    /**
     * Massage colors
     */
    public const COLOR_DANGER = '#A93226';
    public const COLOR_WARNING = '#D68910';
    public const COLOR_INFO = '#2471A3';
    public const COLOR_DEFAULT = '#A6ACAF';

    /**
     * Massage emojis
     */
    public const EMOJI_DANGER = '&#x1F6A8';
    public const EMOJI_WARNING = '&#x1F4E2';
    public const EMOJI_INFO = '&#x1F3C1';
    public const EMOJI_DEFAULT = '&#x1F3C1';

    /**
     * Default Message settings
     */
    public const CARD_TYPE = "MessageCard";
    public const CARD_CONTEXT = "https://schema.org/extensions";

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var array
     */
    private $data = [];

    /**
     * MicrosoftTeamsRecord constructor
     * @param string $title
     * @param string $subject
     */
    public function __construct(string $title, string $subject) {
        $this->title = $title;
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Returns data in Microsoft Teams Card format.
     * @param array $record
     * @return $this
     */
    public function setData(array $record): self
    {
        $this->setType()
            ->setContext()
            ->setThemeColor($record['level'])
            ->setTitle($record['level'])
            ->setText($record['message'])
            ->setSections($record)
        ;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type = self::CARD_TYPE): self
    {
        $this->data['type'] = $type;

        return $this;
    }

    /**
     * @param string $context
     * @return $this
     */
    public function setContext(string $context = self::CARD_CONTEXT): self
    {
        $this->data['context'] = $context;

        return $this;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setThemeColor(int $level): self
    {
        $this->data['themeColor'] = $this->getThemeColor($level);

        return $this;
    }

    /**
     * @param int $level
     * @return $this
     */
    public function setTitle(int $level): self
    {
        $this->data['title'] = sprintf('%s %s', $this->getEmoji($level), $this->title);

        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->data['text'] = $text;

        return $this;
    }

    /**
     * @param array $record
     * @return $this
     */
    public function setSections(array $record): self
    {
        $this->data['sections'] = [];

        $facts = [$this->getFact('Level', $record['level_name'])];

        foreach (array('extra', 'context') as $element) {
            if (empty($record[$element])) {
                continue;
            }

            foreach($record[$element] as $key => $value){
                if($value instanceof \Exception){
                    /** @var \Exception $value */
                    array_push($facts,
                        $this->getFact('message', $value->getMessage()),
                        $this->getFact('Code', $value->getCode()),
                        $this->getFact('File', $value->getFile()),
                        $this->getFact('Line', $value->getLine()),
                        $this->getFact('Trace', $value->getTraceAsString(), true)
                    );
                } else {
                    $facts[] = $this->getFact($key, $value);
                }
            }

            $this->data['sections'][] = [
                'activityTitle' => $this->subject,
                'activitySubtitle' => $record['datetime']->format('Y/m/d g:i A'),
                'facts' => $facts
            ];
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param bool $isQuoted
     * @return array
     */
    public function getFact(string $name, $value, bool $isQuoted = false): array
    {
        $name = trim(str_replace('_', ' ', $name));
        $value = $this->transformValue($value);
        return [
            'name' => ucfirst($name).':',
            'value' => $isQuoted ? sprintf('%s %s %s','<pre>', $value, '</pre>') : $value,
        ];
    }

    /**
     * @param mixed $value
     * @return false|string
     */
    protected function transformValue($value){
        $return = $value;
        if(is_array($value)){
            $value = json_encode($value, JSON_PRETTY_PRINT);
            $return = !empty($value) ? substr($value, 0, 1000) : '';
        } else if(is_string($value)){
            $return = substr($value, 0, 1000);
        }
        return $return;
    }

    /**
     * Returns Microsoft Teams Card message theme color based on log level.
     * @param int $level
     * @return string
     */
    public function getThemeColor(int $level): string
    {
        switch (true) {
            case $level >= Logger::ERROR:
                return static::COLOR_DANGER;
            case $level >= Logger::WARNING:
                return static::COLOR_WARNING;
            case $level >= Logger::INFO:
                return static::COLOR_INFO;
            default:
                return static::COLOR_DEFAULT;
        }
    }

    /**
     * Returns Microsoft Teams Card message emoji based on log level.
     * @param int $level
     * @return string
     */
    public function getEmoji(int $level): string
    {
        switch (true) {
            case $level >= Logger::ERROR:
                return static::EMOJI_DANGER;
            case $level >= Logger::WARNING:
                return static::EMOJI_WARNING;
            case $level >= Logger::INFO:
                return static::EMOJI_INFO;
            default:
                return static::EMOJI_DEFAULT;
        }
    }
}
