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

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class MicrosoftTeamsHandler extends AbstractProcessingHandler
{
    /**
     * MicrosoftTeams Webhook DSN
     * @var string
     */
    private $webhookDsn;

    /**
     * Instance of the MicrosoftTeamsRecord
     * @var MicrosoftTeamsRecord
     */
    private $microsoftTeamsRecord;

    /**
     * MicrosoftTeamsHandler constructor.
     * @param string $webhookDsn
     * @param int    $level
     * @param string $title
     * @param string $subject
     * @param bool   $bubble
     */
    public function __construct(
        string $webhookDsn,
        int $level = Logger::DEBUG,
        string $title = 'Message',
        string $subject = 'Date',
        bool $bubble = true
    )
    {
        parent::__construct($level, $bubble);

        $this->webhookDsn = $webhookDsn;
        $this->microsoftTeamsRecord = new MicrosoftTeamsRecord($title, $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter('[%datetime%] %message% %context% %extra%', 'Y-m-d H:i:s', false, true);
    }

    /**
     * @return string
     */
    public function getWebhookDsn(): string
    {
        return $this->webhookDsn;
    }

    /**
     * @return MicrosoftTeamsRecord
     */
    public function getMicrosoftTeamsRecord(): MicrosoftTeamsRecord
    {
        return $this->microsoftTeamsRecord;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        $postData = $this->microsoftTeamsRecord->setData($record)->getData();
        $dataString = json_encode($postData);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->webhookDsn);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);

        $this->execute($ch);
    }

    /**
     * @param mixed $ch
     * @param int $repeat
     * @return bool|string
     */
    public static function execute($ch, int $repeat = 3)
    {
        while ($repeat--) {
            $response = curl_exec($ch);

            if (false === $response) {
                if(!$repeat){
                    $errno = curl_errno($ch);
                    $error = curl_error($ch);
                    throw new \RuntimeException(sprintf('Curl error %d: %s', $errno, $error));
                }
                continue;
            }

            curl_close($ch);

            return $response;
        }

        return false;
    }
}
