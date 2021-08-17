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
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;

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
            'enableLogging'   => false,
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

    /**
     * @test
     */
    public function it_logs_requests_when_debug_mode_is_set_to_true_and_a_logger_is_provided(): void
    {
        $httpClient     = Mockery::mock(ClientInterface::class);
        $requestFactory = Mockery::mock(RequestFactoryInterface::class);
        $configuration  = Mockery::mock(MaileonConfiguration::class, [
            'getContactEvent' => 'API_Transactional',
            'enableLogging'   => true,
        ]);

        $requestBody = <<<JSON
{
  "typeName": "API_Transactional",
  "import": {
    "contact": {
      "email": "john.doe@example.com",
      "permission": 6
    }
  },
  "content": {
    "subject": "Test email",
    "body_html": [
      "This is a test email."
    ]
  }
}
JSON;

        $request = Mockery::mock(RequestInterface::class, [
            'getUri'     => Mockery::mock(UriInterface::class, [
                '__toString' => 'https://api.maileon.com/1.0/transactions',
            ]),
            'getMethod'  => 'POST',
            'getHeaders' => [
                'Host'          => ['api.maileon.com'],
                'Content-Type'  => ['application/json'],
                'Authorization' => ['Basic SecretApiKey'],
            ],
            'getBody'    => Mockery::mock(StreamInterface::class, [
                '__toString' => $requestBody,
            ]),
        ])
            ->shouldReceive('hasHeader')
            ->once()
            ->with('Authorization')
            ->andReturnTrue()
            ->getMock();

        $requestFactory
            ->shouldReceive('make')
            ->once()
            ->andReturn($request);

        $response = Mockery::mock(ResponseInterface::class, [
            'getStatusCode' => 200,
            'getHeaders'    => [
                'Content-Type' => ['application/json'],
            ],
            'getBody'       => Mockery::mock(StreamInterface::class, [
                '__toString' => '{}',
            ]),
        ]);

        $httpClient
            ->shouldReceive('sendRequest')
            ->once()
            ->with($request)
            ->andReturn($response);

        /** @var LoggerInterface $logger */
        $logger = Mockery::mock(LoggerInterface::class)
            ->shouldReceive('debug')
            ->once()
            ->withArgs(function (string $message, array $context) {
                dump($context);
            })
            ->with('Sending transactional mail request to Maileon.', [
                'request'  => [
                    'uri'     => 'https://api.maileon.com/1.0/transactions',
                    'method'  => 'POST',
                    'headers' => [
                        'Host'          => ['api.maileon.com'],
                        'Content-Type'  => ['application/json'],
                        'Authorization' => ['******'],
                    ],
                    'body'    => $requestBody,
                ],
                'response' => [
                    'status'  => 200,
                    'body'    => '{}',
                    'headers' => [
                        'Content-Type' => ['application/json'],
                    ],
                ],
            ])
            ->getMock();

        $maileonClient = new MaileonClient(
            $httpClient,
            $requestFactory,
            $configuration,
            $logger
        );

        $maileonClient->sendEmail(
            'john.doe@example.com',
            'Test email',
            'This is a test email.'
        );
    }
}
