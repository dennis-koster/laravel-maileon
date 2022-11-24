<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Transports;

use DennisKoster\LaravelMaileon\Contracts\MaileonClientInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class MaileonTransport extends AbstractTransport
{
    public function __construct(
        protected MaileonClientInterface $maileonClient,
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        foreach ($email->getTo() as $recipient) {
            $this->maileonClient->sendEmail(
                $recipient->getAddress(),
                $email->getSubject(),
                $email->getHtmlBody(),
            );
        }
    }

    public function __toString(): string
    {
        return 'maileon';
    }
}
