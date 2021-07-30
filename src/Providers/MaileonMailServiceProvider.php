<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Providers;

use DennisKoster\LaravelMaileon\Managers\MaileonMailManager;
use Illuminate\Mail\MailServiceProvider;

class MaileonMailServiceProvider extends MailServiceProvider
{
    protected function registerIlluminateMailer()
    {
        $this->app->singleton('mail.manager', function($app) {
            return new MaileonMailManager($app);
        });

        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });
    }
}
