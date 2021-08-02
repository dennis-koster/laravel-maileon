<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Tests\Integration\Managers;

use DennisKoster\LaravelMaileon\Managers\MaileonMailManager;
use DennisKoster\LaravelMaileon\Tests\Integration\AbstractIntegrationTest;
use DennisKoster\LaravelMaileon\Transports\MaileonTransport;
use Swis\Http\Fixture\ResponseBuilder;
use Swis\Http\Fixture\ResponseBuilderInterface;

class MaileonMailManagerTest extends AbstractIntegrationTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(ResponseBuilderInterface::class, new ResponseBuilder(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'stubs'));
    }

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
