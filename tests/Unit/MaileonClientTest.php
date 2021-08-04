<?php

declare(strict_types=1);

namespace DennisKoster\LaravelMaileon\Tests\Unit;

use DennisKoster\LaravelMaileon\Contracts\Factories\RequestFactoryInterface;
use DennisKoster\LaravelMaileon\DataObjects\MaileonConfiguration;
use DennisKoster\LaravelMaileon\Enums\RequestMethodsEnum;
use DennisKoster\LaravelMaileon\MaileonClient;
use Mockery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MaileonClientTest extends AbstractUnitTest
{
    /**
     * @test
     */
    public function it_makes_a_transactions_request(): void
    {
        $httpClient     = Mockery::mock(ClientInterface::class);
        $requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $configuration  = Mockery::mock(MaileonConfiguration::class, [
            'getContactEvent' => 'API_Transactional',
        ]);
        $request        = Mockery::mock(RequestInterface::class);

        $requestFactory
            ->shouldReceive('make')
            ->once()
            ->withArgs(function (string $url, RequestMethodsEnum $requestMethod, array $data) {
                return $url === '/transactions'
                    && $requestMethod->is(RequestMethodsEnum::POST())
                    && $data === [
                        'typeName' => 'API_Transactional',
                        'import'   => [
                            'contact' => [
                                'email'      => 'john.doe@example.com',
                                'permission' => 6,
                            ],
                        ],
                        'content'  => [
                            'subject'   => 'Test email',
                            'body_html' => ['This is a test email.'],
                        ],
                    ];
            })
            ->andReturn($request);

        $httpClient
            ->shouldReceive('sendRequest')
            ->once()
            ->with($request)
            ->andReturn(Mockery::mock(ResponseInterface::class));

        $maileonClient = new MaileonClient(
            $httpClient,
            $requestFactory,
            $configuration
        );

        $maileonClient->sendEmail(
            'john.doe@example.com',
            'Test email',
            'This is a test email.'
        );
    }
}
