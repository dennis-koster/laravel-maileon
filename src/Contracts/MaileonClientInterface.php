<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Contracts;

use Psr\Http\Message\ResponseInterface;

interface MaileonClientInterface
{
    public function sendEmail(
        string $recipientEmail,
        string $subject,
        string $contents
    ): ResponseInterface;
}
