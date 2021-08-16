<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Tests\Unit\DataObjects;

use DennisKoster\LaravelMaileon\DataObjects\MaileonConfiguration;
use DennisKoster\LaravelMaileon\Tests\Unit\AbstractUnitTest;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class MaileonConfigurationTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function it_constructs_the_maileon_configuration_object(): void
    {
        $configuration = new MaileonConfiguration(
            'https://api-url.com',
            'some-secret-api-key',
            'API_Transactional',
            null,
        );

        static::assertSame('https://api-url.com', $configuration->getApiUrl());
        static::assertSame('some-secret-api-key', $configuration->getApiKey());
        static::assertSame('API_Transactional', $configuration->getContactEvent());
        static::assertNull($configuration->getHttpClient());
        static::assertNull($configuration->getLogger());
        static::assertFalse($configuration->logRequests());
    }

    /**
     * @test
     */
    public function it_sets_the_http_client_fqn(): void
    {
        $configuration = new MaileonConfiguration(
            'https://api-url.com',
            'some-secret-api-key',
            'API_Transactional',
            ClientInterface::class,
        );

        static::assertSame(ClientInterface::class, $configuration->getHttpClient());
    }

    /**
     * @test
     */
    public function it_sets_the_logger_fqn(): void
    {
        $configuration = new MaileonConfiguration(
            'https://api-url.com',
            'some-secret-api-key',
            'API_Transactional',
            ClientInterface::class,
            LoggerInterface::class,
        );

        static::assertSame(LoggerInterface::class, $configuration->getLogger());
    }

    /**
     * @test
     */
    public function it_sets_log_requests_to_true(): void
    {
        $configuration = new MaileonConfiguration(
            'https://api-url.com',
            'some-secret-api-key',
            'API_Transactional',
            null,
            null,
            true
        );

        static::assertTrue($configuration->logRequests());
    }
}
