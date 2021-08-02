<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Tests\Integration\Managers;

use DennisKoster\LaravelMaileon\Managers\MaileonMailManager;
use DennisKoster\LaravelMaileon\Tests\Integration\AbstractIntegrationTest;
use DennisKoster\LaravelMaileon\Transports\MaileonTransport;

class MaileonMailManagerTest extends AbstractIntegrationTest
{
    /**
     * @test
     */
    public function it_creates_a_maileon_transport(): void
    {
        $maileonMailManager = new MaileonMailManager(
            $this->app
        );

        $result = $maileonMailManager->createTransport([
            'transport' => 'maileon',
        ]);

        static::assertInstanceOf(MaileonTransport::class, $result);
    }
}
