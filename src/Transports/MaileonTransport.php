<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Transports;

use DennisKoster\LaravelMaileon\Contracts\MaileonClientInterface;
use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class MaileonTransport extends Transport
{
    protected MaileonClientInterface $maileonClient;

    public function __construct(MaileonClientInterface $maileonClient)
    {
        $this->maileonClient = $maileonClient;
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null): int
    {
        $this->beforeSendPerformed($message);

        foreach ($message->getTo() as $address => $display) {
            $this->maileonClient->sendEmail(
                $address,
                $message->getSubject(),
                $message->getBody(),
            );
        }

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }
}
