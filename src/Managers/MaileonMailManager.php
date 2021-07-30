<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Managers;

use DennisKoster\LaravelMaileon\Transports\MaileonTransport;
use Illuminate\Mail\MailManager;

class MaileonMailManager extends MailManager
{
    protected function createMaileonTransport(): MaileonTransport
    {
        return $this->app->make(MaileonTransport::class);
    }
}
